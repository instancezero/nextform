<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Binding\SimpleBinding;

/**
 * A skeletal renderer that generates a very basic form.
 */
class SimpleHtml extends Html implements RendererInterface
{

    /**
     * Maps element types to render methods.
     * @var array
     */
    static $renderMethodCache = [];

    public function __construct($options = [])
    {
        parent::__construct($options);
        self::$showDefaultScope = 'form';
        $this->initialize();
    }

    protected function initialize()
    {
        parent::initialize();
        // Reset the context
        $this->context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this->setShow('layout:vertical');
    }

    public function renderTriggers(FieldBinding $binding) : Block
    {
        return new Block;
    }

    public function setOptions($options = [])
    {

    }

    /**
     * Process cell spacing options, called from show().
     *
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoCellspacing($scope, $choice, $values = [])
    {
        // Expecting choice to be "a" or "b".
        // For "a", one or more space delimited single digits from 0 to 5,
        // optionally prefixed with rr-
        //
        // For "b" one or more space-delimited sets of [rr-]xx-n where rr is a
        // renderer selector (sh for simpleHtml), xx is a size specifier,
        // and n is 0 to 5.
        //
        // Specifiers other than bs are ignored the result is a list of
        // classes to be used when spacing between the second and subsequent
        // elements in a cell.

        $styleList = ['display' => 'inline-block', 'padding' => '0.5rem'];
        if ($choice == 'a') {
            $styleList['padding-left'] = '1rem';
        } else {
            foreach ($values as $value) {
                \preg_match(
                    '/(?<prefix>[a-z][a-z0-9]-)?(?<weight>[0-5])/',
                    $value, $match
                );
                if ($match['prefix'] !== '' && $match['prefix'] !== 'sh-') {
                    continue;
                }
                $weight = (int) $match['weight'];
            }
            $styleList['padding-left'] = round(2 * $weight / 5, 2) . 'rem';
        }
        if (!empty($styleList)) {
            $this->showState[$scope]['cellspacing']
                = new Attributes('style', $styleList);
        }
    }

    /**
     * Process layout options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoLayout($scope, $choice, $values = [])
    {
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
        // Clear out anything that might have been set by previous commands.
        unset($this->showState[$scope]['cellElementAttributes']);
        unset($this->showState[$scope]['headingAttributes']);
        unset($this->showState[$scope]['inputWrapperAttributes']);
        $this->showState[$scope]['layout'] = $choice;
        if ($choice === 'horizontal') {
            $this->showDoLayoutAnyHorizontal($scope, $values);
        } elseif ($choice === 'vertical') {
            $this->showDoLayoutAnyVertical($scope, $values);
        }
    }

    /**
     * Process horizontal layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    public function showDoLayoutAnyHorizontal($scope, $values)
    {
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - First column width in CSS units
        // h:nxx/mxx    - CSS units for headers / input elements
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Class for headers
        // h:.c1:.c2    - Class for headers / input elements
        $apply = &$this->showState[$scope];
        switch (count($values)) {
            case 1:
                // No specification, use our default
                $apply['headingAttributes'] = new Attributes(
                    'style',
                    [
                        'display' => 'inline-block',
                        'vertical-align' => 'top',
                        'width' => '25%'
                    ]
                );
                $apply['inputWrapperAttributes'] = new Attributes(
                    'style',
                    [
                        'display' => 'inline-block',
                        'vertical-align' => 'top',
                        'width' => '75%'
                    ]
                );
                break;
            case 2:
                if ($values[1][0] == '.') {
                    // Single class specification
                    $apply['headingAttributes'] = new Attributes('class', [substr($values[1], 1)]);
                } else {
                    // Single CSS units
                    $apply['headingAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[1]
                        ]
                    );
                }
                break;
            default:
                if ($values[1][0] == '.') {
                    // Dual class specification
                    $apply['headingAttributes'] = new Attributes('class', [substr($values[1], 1)]);
                    $apply['inputWrapperAttributes'] = new Attributes('class', [substr($values[2], 1)]);
                } elseif (preg_match('/^[+\-]?[0-9](\.[0-9]*)?$/', $values[1])) {
                    // ratio
                    $part1 = (float) $values[1];
                    $part2 = (float) $values[2];
                    if (!$part1 || !$part2) {
                        throw new \RuntimeException(
                            'Invalid ratio: ' . $values[1] . ':' . $values[2]
                        );
                    }
                    $sum = isset($values[3]) ? $values[3] : ($part1 + $part2);
                    $apply['headingAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part1 / $sum, 3) . '%'
                        ]
                    );
                    $apply['inputWrapperAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part2 / $sum, 3) . '%'
                        ]
                    );
                } else {
                    // Dual CSS units
                    $apply['headingAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[1]
                        ]
                    );
                    $apply['inputWrapperAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[2]
                        ]
                    );
                }
                break;

        }
        if (isset($apply['inputWrapperAttributes'])) {
            $apply['cellElementAttributes'] = $apply['inputWrapperAttributes'];
        }
    }

    /**
     * Process vertical layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    public function showDoLayoutAnyVertical($scope, $values)
    {
        // possible values for arguments:
        // v            - Default, nothing to do
        // v:mxx        - CSS units for input elements
        // v:.c2        - Class for input elements
        // v:m:t        - ratio of inputs over space t.
        $apply = $this->showState[$scope];
        switch (count($values)) {
            case 1:
                // No specification, nothing to do
                break;
            case 2:
                if ($values[1][0] == '.') {
                    // Single class specification
                    $apply['inputWrapperAttributes'] = [
                        'class' => [substr($values[1], 1)],
                    ];
                } else {
                    // Single CSS units
                    $apply['inputWrapperAttributes'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[1],
                        ],
                    ];
                }
                break;
            default:
                if (preg_match('/^[+\-]?[0-9](\.[0-9]*)?$/', $values[1])) {
                    // ratio
                    $part1 = (float) $values[1];
                    if (!$part1) {
                        throw new \RuntimeException(
                            'Zero is invalid in a ratio.'
                        );
                    }
                    $sum = isset($values[2]) ? $values[2] : $part1;
                    $apply['inputWrapperAttributes'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part1 / $sum, 3) . '%'
                        ],
                    ];
                }
                break;
        }
        if (isset($apply['inputWrapperAttributes'])) {
            $apply['cellElementAttributes'] = $apply['inputWrapperAttributes'];
        }
    }

}

