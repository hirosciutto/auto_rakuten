<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

/**
 * search-conditions の BREAD のみに表示する「コピー」アクション。
 * 押下時にその行を is_active=0 で複製する。
 */
class CopySearchConditionAction extends AbstractAction
{
    public function getTitle()
    {
        return 'コピー';
    }

    public function getIcon()
    {
        return 'voyager-documentation';
    }

    public function getPolicy()
    {
        return 'add';
    }

    public function getDataType()
    {
        return 'search_conditions';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-default pull-right',
            'title' => '複製（is_active=0で複製）',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.search-conditions.duplicate', $this->data->{$this->data->getKeyName()});
    }

    /**
     * このアクションは POST で送信する（partial で form として描画するため）
     */
    public function usePost(): bool
    {
        return true;
    }
}
