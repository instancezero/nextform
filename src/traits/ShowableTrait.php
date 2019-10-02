<?php
namespace Abivia\NextForm\Traits;

/**
 * Trait for classes that support a show setting.
 */
trait ShowableTrait
{

    protected $show = '';

    static protected $showDefaultScope = 'undefined';


    public function addShow($show) : self {
        if ($this -> show === '') {
            $this -> show = trim($show);
        } else {
            $this -> show .= '|' . trim($show);
        }
        return $this;
    }

    public function getShow()
    {
        return $this -> show;
    }

    public function setShow($show) : self
    {
        $this -> show = trim($show === null ? '' : $show);
        return $this;
    }

    static public function showGetSetting($text, $defaultScope = '')
    {
        if ($defaultScope === '') {
            $defaultScope = self::$showDefaultScope;
        }
        $first = explode('.', $text);
        if (count($first) == 1) {
            $scope = $defaultScope;
            $setting = $first[0];
        } else {
            $scope = array_shift($first);
            $setting = implode('.', $first);
        }
        return [$scope, $setting];
    }

    static public function showScopeToString($settings, $setScope = '')
    {
        $parts = [];
        foreach ($settings as $setting => $info) {
            $info = implode(':', $info);
            $parts[] = ($setScope === '' ? '' : $setScope . '.')
                . $setting . ':' . $info;
        }
        return implode('|', $parts);
    }

    /**
     * Break a "show" string down into a settings array.
     * @param string $text String of the form scope1.setting1:p1:p2...|scope2.setting2:p1:p2...
     * @return array A list of argument arrays indexed by setting.
     */
    static public function showTokenize($text, $defaultScope = '')
    {
        if ($defaultScope === '') {
            $defaultScope = self::$showDefaultScope;
        }
        $exprs = explode('|', $text);
        $settings = [];
        foreach ($exprs as $clause) {
            $parts = explode(':', $clause);
            list($scope, $setting) = self::showGetSetting(array_shift($parts), $defaultScope);
            if ($setting === '') {
                continue;
            }
            if (!isset($settings[$scope])) {
                $settings[$scope] = [];
            }
            $settings[$scope][$setting] = $parts;
        }
        return $settings;
    }

}
