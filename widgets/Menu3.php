<?php

namespace dmstr\adminlte\widgets;

use dmstr\widgets\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * AdminLTE3 side menu widget
 *
 * @author Hrumpa
 */
class Menu3 extends Menu
{
    /**
     * @inheritdoc 
     */
    public $linkTemplate = '<a href="{url}" class="nav-link{show}">{icon} <p>{label}</p></a>';
    
    /**
     * @inheritdoc
     */
    public $labelTemplate = '{icon} <p>{label}</p>';
    
    /**
     * @var string same as [[linkTemplate]], but for the item with children items
     */
    public $linkTemplateParent = '<a href="{url}" class="nav-link{show}">{icon} <p>{label} <i class="right fas fa-angle-left"></i></p></a>';
    
    /**
     * @var string same as [[labelTemplate]], but for the item with children items
     */
    public $labelTemplateParent = '<p>{label} <i class="right fas fa-angle-left"></i></p>';
    
    /**
     * @var string menu headers template. Option `header` should be set to TRUE 
     * in individual menu items to output it as a menu section header.
     */
    public $headerTemplate = '{label}';
    
    /**
     * @inheritdoc
     */
    public $submenuTemplate = '<ul class="nav nav-treeview">{items}</ul>';
    
    /**
     * @inheritdoc
     */
    public $activateParents = true;
    
    /**
     * @var string default icon to show, whe no 'icon' option isset in individual menu item
     */
    public $defaultIconHtml = '<i class="fas fa-circle nav-icon"></i> ';
    
    /**
     * @var string classes to add to a parent item with active child
     */
    public $classActiveMenu = 'menu-open active';
    
    /**
     * @inheritdoc
     */
    public $options = [
        'class' => 'nav nav-pills nav-sidebar flex-column', 
        'data-widget' => 'treeview', 
    ];
    
    
    public static $iconClassPrefix = 'fas nav-icon fa-';
    
    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $replacements = [
            '{label}' => $item['label'],
            '{icon}' => empty($item['icon']) ? $this->defaultIconHtml
                : '<i class="' . static::$iconClassPrefix . $item['icon'] . '"></i> ',
            '{url}' => isset($item['url']) ? Url::to($item['url']) : 'javascript:void(0);',
            '{show}' => $item['active'] ? ' active' : '',
        ];

        $template = ArrayHelper::getValue($item, 'template', $this->getItemTemplate($item));

        return strtr($template, $replacements);
    }
    
     /**
     * @inheritdoc
     */
    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $itemClasses = empty($item['header']) ? ['nav-item'] : ['nav-header'];
            if ($item['active']) {
                $itemClasses[] = $this->classActiveMenu;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $itemClasses[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $itemClasses[] = $this->lastItemCssClass;
            }
            
            Html::addCssClass($options, $itemClasses);
            
            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $menu .= strtr($this->submenuTemplate, [
                    '{show}' => $item['active'] ? $this->classActiveMenu : '',
                    '{items}' => $this->renderItems($item['items']),
                ]);
                Html::addCssClass($options, 'has-treeview');
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }
        return implode("\n", $lines);
    }
    
    
    /**
     * Finds the right item template
     * 
     * @param array $item
     * @return string
     */
    protected function getItemTemplate($item)
    {
        if (!empty($item['header'])) {
            return $this->headerTemplate;
        } elseif (isset($item['items'])) {
            return isset($item['url']) ? $this->linkTemplateParent : $this->labelTemplateParent; 
        } else {
            return isset($item['url']) ? $this->linkTemplate : $this->labelTemplate;
        }
    }
    
}
