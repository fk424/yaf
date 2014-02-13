<?php
return array(
    //plan
    'newPlan'               => 'ex_new_plan', //add
    'planUpdate'            => 'ex_plan_update',//update
    'planUpdatePauseAll'    => 'ex_plan_update_pause_all',//pause all
    'planUpdateStartAll'    => 'ex_plan_update_start_all',//start all
    'planUpdateDeleteAll'   => 'ex_plan_update_delete_all',//delete all
    'planUpdateExpAll'      => 'ex_plan_update_exp_all',//update exp_amt all

    //group
    'newGroup'                  => 'ex_new_group', //add
    'groupUpdate'               => 'ex_group_update',//update
    'groupUpdatePauseAll'       => 'ex_group_update_pause_all',//pause all
    'groupUpdateStartAll'       => 'ex_group_update_start_all',//start all
    'groupUpdateDeleteAll'      => 'ex_group_update_delete_all',//delete all
    'newCommodityCompete'       => 'groupCommodityCompeteNew',//商品api新建相关词
    'deleteCommodityCompete'    => 'groupCommodityCompeteDel',//商品api删除相关词

    //advert
    'newAdvert'             => 'ex_new_advert', //add
    'newAdvertCommodity'    => 'adCommodityNew', //add
    'newAdvertSearch'       => 'adSearchNew', //add
    'advertUpdate'          => 'ex_advert_update',//update
	'advertBatchUpdate'          => 'ex_advert_update_all',//update	
    'advertUpdatePauseAll'  => 'ex_advert_update_pause_all',//pause all
    'advertUpdateStartAll'  => 'ex_advert_update_start_all',//start all
    'advertUpdateDeleteAll' => 'ex_advert_update_delete_all',//delete all
    'advertOnline'          => 'ex_advert_online',//创意上线
    'advertOffline'         => 'ex_advert_offline',//创意下线

    //keyword
    'newKeyword'        => 'ex_new_keyword', //add
    'keywordUpdate'          => 'ex_keyword_update',//update
	'keywordBatchUpdate'          => 'ex_keyword_batch_update',//update
    'keywordUpdatePauseAll'  => 'ex_keyword_update_pause_all',//pause all
    'keywordUpdateStartAll'  => 'ex_keyword_update_start_all',//start all
    'keywordUpdateDeleteAll' => 'ex_keyword_update_delete_all',//delete all
    'keywordOnline'          => 'ex_keyword_online',//关键词上线
    'keywordOffline'         => 'ex_keyword_offline',//关键词下线

    'redis_search'=>'ex_redis_search_msg',//redis search

    'updateUserEapi' => 'ex_update_user_eapi',
    'updateUserQuota' => 'ex_update_user_quota',

    //egoods 商品搜索
    'eGoodsNew'    => 'e_goods_new',
    'eGoodsUpdate' => 'e_goods_update',
    'egoodsAdvertNewOnLine' => 'e_goods_advert_new_online',   //新商品消息、新建搜索创意日志消息、创意上下线消息组合

    /* 商品搜索关键词相关 */
    'eGoodsNewwords'    => 'e_goods_new_words',         //新增商品关键词
    'eGoodsUpwords'     => 'e_goods_up_words',          //修改商品关键词
    'eGoodsDelwords'    => 'e_goods_del_words',         //删除商品关键词
    'eGoodsEnablewords' => 'e_goods_enable_words',      //启用商品关键词
    'eGoodsPausewords'  => 'e_goods_pause_words',       //暂停商品关键词

    'batch_download'    => 'batch_download',

    //汇总消息
    'materielStatusUpdateAll' => 'ex_materiel_status_update_all',
);
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
