<?php

/**
 * Created by PhpStorm.
 * User: XingRen
 * Date: 2019/4/23
 * Time: 22:10
 */
class MomentsController extends Controller
{
    /**
     * @param int $page
     * @param int $limit
     *
     * @api {get} /moments/list/ Get moment list
     * @apiName GetMoments
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/list
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.moment_id The ID of each moment.
     * @apiSuccess (200) {String} data.moment_content Moment's content.
     * @apiSuccess (200) {Integer} data.moment_like_number The number of thumb up.
     * @apiSuccess (200) {Integer} data.moment_comment_number The comment number.
     * @apiSuccess (200) {Datetime} data.moment_timestamp The moment published time.
     * @apiSuccess (200) {String} data.book_transaction_com_detail The communication id.
     * @apiSuccess (200) {String} data.user_nickname Publisher's nickname.
     * @apiSuccess (200) {String} data.user_username The publisher's name.
     * @apiSuccess (200) {String} data.user_avatar_url The url of user's avatar
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     */
    public function ac_list_get($page, $limit)
    {
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 20;
        }
        if ($limit > 0 && $limit <= 20 && $page > 0) {
            $data = (new MomentsModel())->getMoments($page, $limit);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }

    }

    /**
     * @filter auth canAddContent
     * @param $moment_content
     *
     * @api {post} /moments/content/ Add new moment
     * @apiName AddMoments
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/content
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     * @apiParam {String} moment_content The content of moment.
     *
     * @apiSuccess (201) {String} CREATE Add success.
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     * @apiError (400) {String} INVALID_ARGUMENTS Invalid operation.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_content_post($moment_content)
    {
        if ((new MomentsModel())->timeDiffer()) {
            if (!empty($moment_content)) {
                if ((new MomentsModel())->addMoment($moment_content)) {
                    $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_CONTENT', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canDeleteContent
     *
     * @api {patch} /moments/content/ Delete moment
     * @apiName DeleteMoments
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/content
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     * @apiParam {String} moment_id The moment id.
     *
     * @apiSuccess (200) {String} OK Delete success.
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     * @apiError (400) {String} INVALID_ARGUMENTS Invalid operation.
     * @apiError (400) {String} INVALID_MOMENT_ID Moment ID is error.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */

    public function ac_content_patch()
    {
        $request = BunnyPHP:: getRequest();
        $request->process();
        $moment_id = $request['moment_id'];
        if ((new MomentsModel())->timeDiffer()) {
            if ((new MomentsModel())->checkMomentId($moment_id)) {
                if ((new MomentsModel())->deleteMoment($moment_id) != null) {
                    $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_MOMENT_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @param $moment_id
     * @param $page
     * @param $limit
     *
     * @api {get} /moments/comment/ Get moment comment list
     * @apiName GetMomentCommentList
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/comment
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {String} moment_id The moment id.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.moment_comment_id The id for each comment.
     * @apiSuccess (200) {String} data.moment_comment The content of comment.
     * @apiSuccess (200) {Datetime} data.moment_publish_time Moment published time.
     * @apiSuccess (200) {Integer} data.user_id The user id.
     * @apiSuccess (200) {String} data.user_nickname The user nickname of comment publisher.
     * @apiSuccess (200) {String} data.user_username The user name of comment publisher.
     * @apiSuccess (200) {String} data.user_avatar_url The url of user's avatar
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     */
    public function ac_comment_get($moment_id, $page, $limit)
    {
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 20;
        }
        if ($limit > 0 && $limit <= 20 && $page > 0) {
            $data = (new MomentsCommentsModel())->getMomentComments($moment_id, $page, $limit);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canAddComment
     * @param $moment_id
     * @param $moment_comment
     *
     * @api {post} /moments/comment/ Add new moment comment
     * @apiName AddMomentComment
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/comment
     * @apiVersion 0.1.0
     *
     * @apiparam {Integer} moment_id The moment id.
     * @apiparam {String} user_token The user token.
     * @apiparam {String} moment_comment The content of moment comment.
     *
     * @apiSuccess (201) {String} CREATED Add success.
     *
     * @apiError (400) {String} INVALID_MOMENT_ID Arguments the moment id does not exist.
     * @apiError (400) {String} INVALID_ARGUMENTS The operation can not be created.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_comment_post($moment_id, $moment_comment)
    {
        if ((new MomentsCommentsModel())->timeDiffer()) {
            $user_id = (new TokensModel())->getUserIdByToken();
            if ((new MomentsModel())->checkMomentId($moment_id)) {
                if ((new MomentsCommentsModel())->addMomentComment($moment_id, $user_id, $moment_comment)) {
                    $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_MOMENT_ID', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }


    /**
     * @filter auth canDeleteComment
     *
     * @api {patch} /moments/comment/ Delete moment comment
     * @apiName DeleteMomentComment
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/comment
     * @apiVersion 0.1.0
     *
     * @apiparam {String} user_token The user token.
     * @apiparam {Integer} moment_id The moment id.
     * @apiparam {Integer} moment_comment_id The moment comment id.
     *
     * @apiSuccess (200) {String} OK Delete success.
     * @apiError (400) {String} INVALID_ARGUMENTS Arguments the operation can not be created.
     * @apiError (400) {String} INVALID_MOMENT_ID Arguments the moment does not exist.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_comment_patch()
    {
        $request = BunnyPHP:: getRequest();
        $request->process();
        $moment_id = intval($request['moment_id']);
        $moment_comment_id = intval($request['moment_comment_id']);


        if ((new MomentsCommentsModel())->timeDiffer()) {
            if ((new MomentsCommentsModel())->checkCommentStatus($moment_id)) {
                if ((new MomentsCommentsModel())->deleteMomentComment($moment_id, $moment_comment_id) != null) {
                    $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_MOMENT_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @param $moment_id
     *
     * @api {get}/moments/like/ Get list of like
     * @apiName GetLikeList
     * @apiGroup Moments
     * @apiPermission admit
     * @apiSampleRequest https://dev.iecho.cc/api/moments/like
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} moment_id The moment id.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.user_id The id for each user.
     * @apiSuccess (200) {String} data.user_nickname The nickname of user.
     * @apiSuccess (200) {String} data.user_username The name of user.
     * @apiSuccess (200) {Integer} data.moment_id The moment id.
     * @apiSuccess (200) {String} data.moment_like_status The status of like.
     * @apiError (400) {String} INVALID_MOMENT_ID Arguments the moment does not exist.
     */
    public function ac_like_get($moment_id)
    {
        if ((new MomentsModel())->checkMomentId($moment_id)) {
            $data = (new MomentsLikeModel())->likeList($moment_id);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_MOMENT_ID', 'code' => 400, 'extra' => ''])->render();
        }
    }


    /**
     * @filter auth canAddLike
     * @param $moment_id
     *
     * @api {post} /moments/like/ Like
     * @apiName AddMomentLike
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/like
     * @apiVersion 0.1.0
     *
     * @apiparam {String} user_token The user token
     * @apiparam {Integer} moment_id The moment id
     *
     * @apiSuccess (201) {String} CREATED Add success.
     *
     * @apiError (400) {String} INVALID_ARGUMENTS Arguments the operation can not be created.
     * @apiError (400) {String} INVALID_MOMENT_ID Arguments the moment does not exist.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_like_post($moment_id)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ((new MomentsLikeModel())->timeDiffer()) {
            if ((new MomentsModel())->checkMomentId($moment_id)) {
                if ((new MomentsLikeModel())->like($user_id, $moment_id) != null) {
                    $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_MOMENT_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canDeleteLike
     *
     * @api {patch} /moments/like/ Dislike
     * @apiName DeleteMomentLike
     * @apiGroup Moments
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/moments/like
     * @apiVersion 0.1.0
     *
     * @apiparam {String} user_token The user token
     * @apiparam {Integer} moment_id The moment id
     *
     * @apiSuccess (201) {String} OK Delete success.
     *
     * @apiError (400) {String} INVALID_ARGUMENTS Arguments the operation can not be created.
     * @apiError (400) {String} INVALID_MOMENT_ID Arguments the moment does not exist.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_like_patch()
    {
        $request = BunnyPHP:: getRequest();
        $request->process();
        $moment_id = $request['moment_id'];
        $user_id = (new TokensModel())->getUserIdByToken();
        if ((new MomentsLikeModel())->timeDiffer()) {
            if ((new MomentsModel())->checkMomentId($moment_id)) {
                if ((new MomentsLikeModel())->dislike($user_id, $moment_id) != null) {
                    $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_MOMENT_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

}