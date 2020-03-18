<?php

/**
 * Created by PhpStorm.
 * User: Xinyun
 * Date: 2019/4/23
 * Time: 11:23
 */

class LostFoundController extends Controller
{
    /**
     * @param $page
     * @param $limit
     * @param $lost_type
     * @param $lost_status
     *
     * @api {get} /LostFound/list/ Get lost and found list
     * @apiName GetLostFound
     * @apiGroup Lost Found
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/lostFound/list/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {Integer} lost_type The type, 0 is lost, 1 is found.
     * @apiParam {Integer} lost_status The state type.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.user_id  Author's id.
     * @apiSuccess (200) {String} data.user_nickname Author's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url  Author's avatar.
     * @apiSuccess (200) {Integer} data.lost_id The id.
     * @apiSyccess (200) {String} data.lost_title The title.
     * @apiSuccess (200) {String} data.lost_detail The detail.
     * @apiSuccess (200) {String} data.lost_com_type The communication ways.
     * @apiSuccess (200) {String} data.lost_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.lost_time Lost found published time.
     * @apiSuccess (200) {Datetime} data.lost_update_time The time when finish the found.
     *
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     * @apiError (400) {String} INVALID_AGREEMENT Status not accepted.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_list($page, $limit, $lost_type, $lost_status)
    {
        if (empty($limit) || empty($page) || (empty($lost_type) && $lost_type != '0') || (empty($lost_status) && $lost_status != '0') || (empty($lost_type) && $lost_type != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($limit < 0 && $limit > 20 && $page < 0) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } elseif (($lost_status != 0 && $lost_status != 1 && $lost_status != 3) || ($lost_type != 0 && $lost_type != 1 && $lost_type != 2)) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENT', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new LostFoundModel())->getLostAndFound($page, $limit, $lost_type, $lost_status);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }

    /**
     * @filter auth can add
     * @param $user_token
     * @param $lost_type
     * @param $lost_title
     * @param $lost_detail
     * @param $lost_com_type
     * @param $lost_com_detail
     *
     * @api {post} /lostFound/record/ Publish new record
     * @apiName AddLostFound
     * @apiGroup Lost Found
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/lostFound/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token The user access token.
     * @apiParam {Integer} lost_type the type, 0 is lost, 1 is found.
     * @apiParam {Integer} lost_title The title.
     * @apiParam {String} lost_detail The detail of the record.
     * @apiParam {Integer} lost_com_type The communication way.
     * @apiParam {String} lost_com_detail The communication number.
     *
     * @apiSuccess (201) {String} CREATED Add success.
     *
     * @apiError (406) {String} NOT_ACCEPTABLE Arguments access too frequent.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (400) {String} INVALID_AGREEMENT Arguments invalid
     */
    public function ac_record_post($user_token, $lost_type, $lost_title, $lost_detail, $lost_com_type, $lost_com_detail)
    {
        $user_id = (new TokensModel())->getUserIdByToken($user_token);
        if ($lost_com_type == 0) {
            $lost_com_detail = ((new UsersModel())->getUserPhoneByUserId($user_id));
        }

        if (empty($lost_title) || (empty($lost_type) && $lost_type != '0') || (empty($lost_com_type) && $lost_com_type != '0') || empty($lost_com_detail)) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif (($lost_type != 0 && $lost_type != 1) || ($lost_com_type != 0 && $lost_com_type != 1 && $lost_com_type != 2 && $lost_com_type != 3)) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new LostFoundModel())->checkLostAndFound($user_id, $lost_title)) {
            $this->assignAll(['msg' => 'NOT_ACCEPTABLE', 'code' => 406, 'extra' => ''])->render();
        } elseif ((new LostFoundModel())->addLostAndFound($lost_type, $user_id, $lost_title, $lost_detail, $lost_com_type, $lost_com_detail)) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @api {patch} /lostFound/record/ Complete record
     * @apiName UpdateLostFound
     * @apiGroup Lost Found
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/lostFound/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token The user access token.
     * @apiParam {Integer} lost_id The information id
     * @apiParam {Integer} lost_status Update or delete
     *
     * @apiSuccess (201) {String} CREATED update success.
     *
     * @apiError (400) {String} INVALID_AGREEMENT Arguments invalid
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_record_patch()
    {
        $request = BunnyPHP::getRequest();
        $request->process();
        $lost_id = $request['lost_id'];
        $lost_status = $request['lost_status'];
        $user_id = (new TokensModel())->getUserIdByToken();

        if (empty($lost_status) && empty($lost_id)) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($lost_status != 1 && $lost_status != 2) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENT', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new LostFoundModel())->updateLostAndFound($lost_id, $user_id, $lost_status)) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_ARGUMENT', 'code' => 406, 'extra' => ''])->render();
        }
    }

    /**
     * @param $page
     * @param $limit
     * @param $keyword
     *
     * @api {get} /lostFound/search/ Search record
     * @apiName searchLostFound
     * @apiGroup Lost Found
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/lostFound/search/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {Integer} keyword The keyword.
     *
     * @apiSuccess (200) {String} OK Search success.
     * @apiSuccess (200) {Object[]} Data Response payload.
     * @apiSuccess (200) {Integer} data.user_id Author's id.
     * @apiSuccess (200) {String} data.user_nickname Author's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url Author's avatar.
     * @apiSyccess (200) {Integer} data.lost_id The title.
     * @apiSyccess (200) {String} data.lost_title The title.
     * @apiSuccess (200) {String} data.lost_detail The detail.
     * @apiSuccess (200) {String} data.lost_com_type The communication ways.
     * @apiSuccess (200) {String} data.lost_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.lost_time Book transaction published time.
     *
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     */
    public function ac_search_get($page, $limit, $keyword)
    {
        if (empty($keyword) || empty($page) || empty($limit)) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($page < 0 || $limit < 0 || $limit > 20) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new LostFoundModel())->searchLostAndFound($page, $limit, $keyword);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }
}