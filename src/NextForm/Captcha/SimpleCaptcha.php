<?php

namespace Abivia\NextForm\Captcha;

use Abivia\NextForm\Contracts\CaptchaInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Render\Block;
/**
 * SimpleCaptcha
 */
class SimpleCaptcha implements CaptchaInterface
{
    private $answer;

    private $tricks = [];
    private $trickStyles;
    private $trickZero;

    /**
     * Generate a series of style definitions that randomly distribute
     * positive and negative deltas across 9 before CSS rules, 9 after
     * CSS rules and one offset.
     */
    private function generateTricks()
    {
        $tricks = [];
        for ($ind = -9; $ind < 10; ++$ind) {
            // Generate a unique random style
            while (true) {
                $rule = 'nf_ca';
                for ($posn = 0; $posn < 5; ++$posn) {
                    $rule .= chr(ord('a') + random_int(0, 25));
                }
                if (!isset($tricks[$rule])) {
                    break;
                }
            }
            $tricks[$rule] = $ind;
            if ($ind === 0) {
                $this->trickZero = $rule;
            }
        }

        // Now sort to randomize the offsets
        ksort($tricks);

        // Generate CSS, assign half to the before role, half to after
        $this->trickStyles = '';
        $this->tricks = ['after' => [], 'before' => []];
        $ind = 0;
        foreach ($tricks as $rule => $offset) {
            if ($offset === 0) {
                $this->trickStyles .= ".$rule{left:-"
                    . random_int(15000, 25000) . "px;position:absolute;}";
                continue;
            }
            $slot = $ind && 1 ? 'after' : 'before';
            $style = ".$rule:$slot{content:\"";
            if ($offset > 0) {
                $style .= ' \a0+\a0 ' . $offset;
            } else {
                $style .= ' \a0-\a0 ' . abs($offset);
            }
            $style .='";font-style:normal;width:0}';
            $this->trickStyles .= $style;
            $this->tricks[$slot][$rule] = $offset;
            ++$ind;
        }
    }

    public function render(
        RenderInterface $engine,
        Binding $binding,
        $options = []
    ) : Block {
        if (empty($this->tricks)) {
            $this->generateTricks();
        }
        if ($this->answer === null) {
            $this->answer = random_int(20, 80);
        }
        // pick the before and after classes
        $before = array_rand($this->tricks['before']);
        $after = array_rand($this->tricks['after']);

        // Compute the question by subtracting from the answer
        $start = $this->answer - $this->tricks['before'][$before]
            - $this->tricks['after'][$after];
        // The question is composed of the before class, the decoy
        // and the after class
        $signs = ['&nbsp;+&nbsp;' => '+', '&nbsp;-&nbsp;' => '-'];
        $question = $start . '<i class="' . $before . '"></i>'
            . '<span class="' . $this->trickZero . '">'
            . array_rand($signs) . random_int(10, 19)
            . array_rand($signs) . random_int(10, 19)
            . '</span>'
            . '<i class="' . $after . '"></i>&nbsp;=&nbsp;';

        // Generate an input element
        $display = FieldBinding::build('text');

        // Create a copy of the source labels
        $displayLabels = clone $binding->getLabels();

        // Customize and render
        $displayLabels->set(
            'before',
            "Please solve :question",
            false,
            ['question' => $question],
            true
        );
        $display->setLabels($displayLabels);
        $display->translate($binding->getTranslator());
        $block = $engine->render($display);
        $block->styles = $this->trickStyles;
        $block->data[self::class] = ['answer' => $this->answer];

        return $block;
    }

    public function reset()
    {
        $this->answer = null;
    }
}
