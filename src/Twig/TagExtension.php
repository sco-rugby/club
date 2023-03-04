<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TagExtension extends AbstractExtension {

    public function getFilters() {
        return [
            new TwigFilter('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFilter('button', [$this, 'button'], ['is_safe' => ['html']]),
            new TwigFilter('attributes', [$this, 'attributes']),
            new TwigFilter('substr', [$this, 'substr'])
        ];
    }

    public function getFunctions() {
        return [
            new TwigFunction('tag', [$this, 'tag'], ['is_safe' => ['html']]),
            new TwigFunction('content', [$this, 'content'], ['is_safe' => ['html']]),
            new TwigFunction('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFunction('button', [$this, 'button'], ['is_safe' => ['html']]),
            new TwigFunction('substr', [$this, 'substr'])
        ];
    }

    public function tag($name, $options = [], $xthml = true): string {
        if (!$name) {
            return '';
        }
        $startTag = '<' . $name . ' ';
        $endTag = ($xthml) ? ' />' : ' ></' . $name . '>';
        return $startTag . $this->attributes($options) . $endTag;
    }

    public function content($name, $content = '', $options = []): string {
        if (!$name) {
            return '';
        }
        return '<' . $name . ' ' . $this->attributes($options) . '>' . $content . '</' . $name . '>';
    }

    public function attributes(array $options = array()): string {
        $attr = [];
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $attr[] = $key . '="' . $this->escape($value) . '"';
        }
        return implode(' ', $attr);
    }

    private function escape($value) {
        return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', htmlspecialchars($value));
    }

    public function icon($icon, $options = []): string {
        $class = [];
        $tag = 'i';
        $xthml = false;
        $content = null;
        if (in_array(substr($icon, -1, 4), ['.png', '.jpg' . 'svg'])) {
            /**
             *  image
             */
            $options['src'] = $icon;
            $tag = 'img';
            $xthml = true;
        } elseif ('#' == substr($icon, 0, 1)) {
            /**
             *  Included Fontawesome sprite
             */
            $content = $this->tag('use', ['xlink:href' => $icon]);
            $tag = 'svg';
        } elseif ('fa' == substr($icon, 0, 2)) {
            /**
             * FontAwesome https://fontawesome.com/icons
             */
            $class = [$icon];
        } elseif ('bi' == substr($icon, 0, 2)) {
            /**
             * Bootstrap icons https://icons.getbootstrap.com/
             */
            $class = ['bi', $icon];
        } elseif (in_array(substr($icon, 0, 3), ['mi-', 'ms-'])) {
            /**
             * Google Material Icons | Material Symbols
             * https://fonts.google.com/icons?icon.set=Material+Symbols
             * https://fonts.google.com/icons?icon.set=Material+Icons
             */
            $tag = 'span';
            $content = substr($icon, 3);
            switch (substr($icon, 0, 3)) {
                case 'mi-':
                    $classSuffix = 'material-icons';
                    $styles = ['outlined', 'round', 'sharp', 'two-tone'];
                    break;
                case 'ms-':
                    $classSuffix = 'material-symbols';
                    $styles = ['outlined', 'rounded', 'sharp'];
                    break;
            }
            $class = [$classSuffix];
            if (array_key_exists('style', $options)) {
                if (in_array($options['style'], $styles)) {
                    $class = [$classSuffix . '-' . $options['style']];
                } else {
                    throw \Exception(sprintf('%s should be %s. %s given', $classSuffix, implode(', ', $styles), $options['style']));
                }
                unset($options['style']);
                $class = array_merge($class, ['me-2']);
            }
        } else {
            /**
             * Feather Icons https://feathericons.com/
             */
            $options['data-feather'] = $icon;
        }
        // Add class according to the icon
        if (array_key_exists('class', $options)) {
            $class = array_merge($class, (array) $options['class']);
        }
        $options['class'] = $class;
        //
        if (null === $content) {
            return $this->tag($tag, $options, $xthml);
        } else {
            return $this->content($tag, $content, $options);
        }
    }

    public function button($label, $options = []): string {
        $data = [];
        $attr['role'] = 'button';
        $attr['href'] = '#';
        // Action, if not defined
        if (!array_key_exists('action', $options)) {
            $options['action'] = null;
        }
        // Class
        $class = ['btn', 'btn-action'];
        if (array_key_exists('class', $options)) {
            $class = array_merge($class, (array) $options['class']);
            unset($options['class']);
        }
        $attr['class'] = implode(' ', $class);
        // Button attributes
        if (in_array($options['action'], ['submit', 'reset'])) {
            $attr['type'] = $options['action'];
        } else {
            $attr['type'] = 'button';
        }
        if (array_key_exists('id', $options)) {
            $attr['id'] = $options['id'];
            unset($options['id']);
        }
        if (array_key_exists('tooltip', $options)) {
            $data['bs-toggle'] = 'tooltip';
            $attr['title'] = $options['tooltip'];
            unset($options['tooltip']);
        }
        if (array_key_exists('modal', $options)) {
            $data['bs-toggle'] = 'modal';
            $data['bs-target'] = '#' . $options['modal'];
            unset($options['modal']);
        }
        if (array_key_exists('offcanvas', $options)) {
            $data['bs-toggle'] = 'offcanvas';
            $attr['href'] = '#' . $options['offcanvas'];
            unset($options['offcanvas']);
        }
        if (array_key_exists('collapse', $options)) {
            $data['bs-toggle'] = 'collapse';
            $data['bs-target'] = '#' . $options['collapse'];
            unset($options['collapse']);
        }
        // Data and options in attibutes
        //$data['action'] = $options['action'];
        //unset($options['action']);
        ksort($data);
        foreach ($data as $key => $value) {
            $attr["data-" . $key] = $value;
        }
        forEach ($options as $key => $value) {
            $attr[$key] = $value;
        }
        // Render
        $contenu = '';
        if (!array_key_exists('icon', $options)) {
            switch ($options['action']) {
                case 'add':
                    $options['icon'] = 'bi-plus-circle';
                    break;
                case 'submit':
                    $options['icon'] = 'bi-save';
                    break;
                case 'reset':
                    $options['icon'] = 'bi-arrow-counterclockwise';
                    break;
            }
        }
        if (array_key_exists('icon', $options)) {
            $contenu = $this->icon($options['icon'], ['class' => 'align-self-center']);
        }
        if ($label != null) {
            $contenu .= '<span class="btn-label ms-2">' . $label . '</span>';
        }
        return $this->content('button', $contenu, $attr);
    }

    public function substr($string, int $offset, ?int $length = null): string {
        return substr($string, $offset, $length);
    }

}
