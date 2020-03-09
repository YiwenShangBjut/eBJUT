<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/4/28
 * Time: 10:43
 */

class ItemsTransactionController extends Controller
{

    /**
     * @param int $page
     * @param int $limit
     * @param int $item_transaction_status
     *
     * @api {get} /itemsTransaction/list/ Get item transaction list
     * @apiName GetItemsTransaction
     * @apiGroup Item Transaction
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/itemsTransaction/list/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {Integer} item_transaction_status The state type, 0 = not sole, 1 = sold, 2 = delete, 3 = all
     *
     * @apiSuccess (200) {String} OK get item_transaction success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.user_id Seller's id.
     * @apiSuccess (200) {String} data.user_nickname Seller's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url Seller's avatar.
     * @apiSyccess (200) {Integer} data.item_transaction_id The id.
     * @apiSyccess (200) {String} data.item_transaction_title The title.
     * @apiSuccess (200) {String} data.item_transaction_detail The detail.
     * @apiSuccess (200) {Numeric} data.item_transaction_price The expected price.
     * @apiSuccess (200) {String} data.item_transaction_com_type The communication ways.
     * @apiSuccess (200) {String} data.item_transaction_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.item_transaction_time Item transaction published time.
     * @apiSuccess (200) {Datetime} data.item_transaction_update_time Item transaction finish time.
     *
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     * @apiError (400) {String} INVALID_AGREEMENT Status not accepted.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_list_get($page, $limit, $item_transaction_status)
    {
        if (empty($page) || empty($limit) || (empty($item_transaction_status) && $item_transaction_status != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($limit < 0 && $limit > 20 && $page < 0) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($item_transaction_status != 0 && $item_transaction_status != 1 && $item_transaction_status != 3) {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new ItemsTransactionModel())->getItemTransaction($page, $limit, $item_transaction_status);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }

    /**
     * @filter auth canAddItemTransaction
     * @param $item_transaction_title
     * @param $item_transaction_detail
     * @param $item_transaction_price
     * @param $item_transaction_com_type
     * @param $item_transaction_com_detail
     *
     * @api {post} /itemsTransaction/add/ Publish transaction
     * @apiName AddItemsTransaction
     * @apiGroup Item Transaction
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/itemsTransaction/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     * @apiParam {String} item_transaction_title The title of the information
     * @apiParam {String} item_transaction_detail The detail of the information
     * @apiParam {Numeric} item_transaction_price The transaction expected price
     * @apiParam {Integer} item_transaction_com_type The communication way
     * @apiParam {String} item_transaction_com_detail The communication number
     *
     * @apiSuccess (201) {String} CREATE Add Item Transaction success.
     *
     * @apiError (400) {String} INVALID_AGREEMENT Arguments invalid.
     * @apiError (406) {String} NOT_ACCEPTABLE Arguments access too frequent.
     * @apiError (400) {String} OUT_OF_RANGE Price not accepted.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_record_post($item_transaction_title, $item_transaction_detail, $item_transaction_price, $item_transaction_com_type, $item_transaction_com_detail)
    {

        $user_id = (new TokensModel())->getUserIdByToken();
        if ($item_transaction_com_type == 0) {
            $item_transaction_com_detail = ((new UsersModel())->getUserPhoneByUserId($user_id));
        }

        if (empty($item_transaction_title) || empty($item_transaction_com_detail) || (empty($item_transaction_com_type) && $item_transaction_com_type != '0') || (empty($item_transaction_price) && $item_transaction_price != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($item_transaction_price < 0 || $item_transaction_price > 1000) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($item_transaction_com_type != 0 && $item_transaction_com_type != 1 && $item_transaction_com_type != 2 && $item_transaction_com_type != 3) {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new ItemsTransactionModel())->checkItemTransaction($user_id, $item_transaction_title)) {
            $this->assignAll(['msg' => 'NOT_ACCEPTABLE', 'code' => 406, 'extra' => ''])->render();
        } elseif ((new ItemsTransactionModel())->addItemTransaction($user_id, $item_transaction_title, $item_transaction_detail, $item_transaction_price, $item_transaction_com_type, $item_transaction_com_detail)) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canUpdateBookTransaction
     *
     * @api {patch} /itemsTransaction/record/ Complete transaction
     * @apiName UpdateItemsTransaction
     * @apiGroup Item Transaction
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/itemsTransaction/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     * @apiParam {Integer} item_transaction_id The information id
     * @apiParam {Integer} item_transaction_status Update or delete
     *
     * @apiSuccess (201) {String} CREATE Update success
     *
     * @apiError (400) {String} INVALID_AGREEMENT Arguments invalid
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_record_patch()
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        $request = BunnyPHP::getRequest();
        $request->process();
        $item_transaction_id = $request['item_transaction_id'];
        $item_transaction_status = $request['item_transaction_status'];

        if (empty($item_transaction_status) || (empty($item_transaction_id))) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($item_transaction_status != 1 || $item_transaction_status != 2) {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new ItemsTransactionModel())->updateItemTransaction($item_transaction_id, $user_id, $item_transaction_status)) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param int $page
     * @param int $limit
     * @param $keyword
     *
     * @api {get} /itemsTransaction/search/ Search item
     * @apiName searchItemsTransaction
     * @apiGroup Item Transaction
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/itemsTransaction/search/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {Integer} keyword The keyword.
     *
     * @apiSuccess (200) {String} OK Search success.
     * @apiSuccess (200) {Object[]} Data Response payload.
     * @apiSuccess (200) {String} data.user_id Seller's id.
     * @apiSuccess (200) {String} data.user_nickname Seller's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url Seller's avatar.
     * @apiSyccess (200) {Integer} data.item_transaction_id The title.
     * @apiSyccess (200) {String} data.item_transaction_title The title.
     * @apiSuccess (200) {String} data.item_transaction_detail The detail.
     * @apiSuccess (200) {Numeric} data.item_transaction_price The expected price
     * @apiSuccess (200) {String} data.item_transaction_com_type The communication ways.
     * @apiSuccess (200) {String} data.item_transaction_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.item_transaction_time Item transaction published time.
     *
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     */
    public function ac_search_get($page, $limit, $keyword)
    {
        if (empty($keyword) || empty($page) || empty($limit)) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($limit < 0 || $limit > 20 || $page < 0) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new ItemsTransactionModel())->searchItemTransaction($page, $limit, $keyword);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }
}