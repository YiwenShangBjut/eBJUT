<?php

/**
 * Created by PhpStorm.
 * User: Xinyun
 * Date: 2019/4/24
 * Time: 0:26
 */

class LostCardController extends Controller
{

    /**
     * @param $page
     * @param $limit
     * @param $lost_card_type
     * @param $lost_card_status
     *
     * @api {get} /LostCard/list/ Get lost card list
     * @apiName GetLostCard
     * @apiGroup Lost Card
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/lostCard/list/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {Integer} lost_card_type The type, 0 is lost, 1 is found.
     * @apiParam {Integer} lost_card_status The state type.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.user_id Author's id.
     * @apiSuccess (200) {String} data.user_nickname Author's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url Author's avatar.
     * @apiSuccess (200) {Integer} data.lost_card_id The id.
     * @apiSyccess (200) {String} data.lost_student_id The lost card's id.
     * @apiSuccess (200) {String} data.lost_card_detail The detail.
     * @apiSuccess (200) {String} data.lost_card_com_type The communication ways.
     * @apiSuccess (200) {String} data.lost_card_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.lost_card_time Lost card published time.
     * @apiSuccess (200) {Datetime} data.lost_card_update_time The time when finish the found.
     *
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     * @apiError (400) {String} INVALID_AGREEMENT Status not accepted.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_list_get($page, $limit, $lost_card_type, $lost_card_status)
    {
        if (empty($page) || empty($limit) || empty($lost_card_status) && $lost_card_status != '0'|| (empty($lost_card_type) && $lost_card_type != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => $lost_card_type])->render();
        } elseif ($limit < 0 || $limit > 20 || $page < 0) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } elseif (($lost_card_status != 0 && $lost_card_status != 1 && $lost_card_status != 3) || ($lost_card_type != 0 && $lost_card_type != 1 && $lost_card_type != 2)) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENT', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new LostStudentCardModel())->getLostCard($page, $limit, $lost_card_type, $lost_card_status);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }

    /**
     * @filter auth can add lost card record
     * @param $lost_card_type
     * @param $lost_student_id
     * @param $lost_card_detail
     * @param $lost_card_com_type
     * @param $lost_card_com_detail
     *
     * @api {post} /lostCard/record/ Publish new record
     * @apiName AddLostCard
     * @apiGroup Lost Card
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/lostCard/record/
     * @apiVersion 0.1.0
     *
     * @apiParam{String} user_token The user access token.
     * @apiParam {Integer} lost_card_type The type, 0 is lost, 1 is found.
     * @apiParam {Integer} lost_student_id The lost card's student id.
     * @apiParam {String} lost_card_detail The detail of the record.
     * @apiParam {Integer} lost_card_com_type The communication way.
     * @apiParam {String} lost_card_com_detail The communication number.
     *
     * @apiSuccess (201) {String} CREATED Add success.
     *
     * @apiError (406) {String} NOT_ACCEPTABLE Arguments access too frequent.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (400) {String} INVALID_AGREEMENT Arguments invalid
     */
    public function ac_record_post($lost_card_type, $lost_student_id, $lost_card_detail, $lost_card_com_type, $lost_card_com_detail)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ($lost_card_com_type == 0) {
            $lost_card_com_detail = ((new UsersModel())->getUserPhoneByUserId($user_id));
        }

        if (empty($lost_student_id) || empty($lost_card_com_detail) || empty($lost_card_com_detail) && $lost_card_com_detail != '0' || (empty($lost_card_type) && $lost_card_type != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif (($lost_card_type != 0 && $lost_card_type != 1) || ($lost_card_com_type != 0 && $lost_card_com_type != 1 && $lost_card_com_type != 2 && $lost_card_com_type != 3)) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new LostStudentCardModel())->checkLostCard($user_id, $lost_student_id)) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new LostStudentCardModel())->addLostCard($lost_card_type, $user_id, $lost_student_id, $lost_card_detail, $lost_card_com_type, $lost_card_com_detail) > 0) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
            if ($lost_card_type == 1) {
                $student_id = ((new UsersModel())->getUserIdByStudentCard($lost_student_id));
                if (!empty($student_id)) {
                    $student_phone = ((new UsersModel())->getUserPhoneByUserId($student_id));
                    $nickname = ((new UsersModel())->getNicknameByUserId($student_id));
                    ((new SmsModel())->sendSms($student_phone, $nickname));
                }
            }
        } else {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @api {patch} /lostCard/record/ Complete record
     *
     * @apiName UpdateLostCard
     * @apiGroup Lost Card
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/lostCard/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token The user access token.
     * @apiParam {Integer} lost_card_id The information id
     * @apiParam {Integer} lost_card_status Update or delete
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
        $lost_card_id = $request['lost_card_id'];
        $lost_card_status = $request['lost_card_status'];
        $user_id = (new TokensModel())->getUserIdByToken();
        $student_id = ((new UsersModel())->getStudentIdByUserId($user_id));

        if (empty($lost_card_id) || empty($lost_card_status)) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($lost_card_status != 1 && $lost_card_status != 2) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new LostStudentCardModel())->updateLostCard($lost_card_id, $user_id, $lost_card_status, $student_id) > 0) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }
}