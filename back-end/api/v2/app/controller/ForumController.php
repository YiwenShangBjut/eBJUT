<?php

/**
 * Created by PhpStorm.
 * User: Xingren
 * Date: 2019/5/6
 * Time: 13:13
 */
class ForumController extends Controller
{
    /**
     * @param $category_id
     * @param $page
     * @param $limit
     *
     * @api {get} /forum/list/ Get list of thread title
     * @apiName GetThreadTitle
     * @apiGroup Forum
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/forum/list
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} category_id The type of thread.
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.forum_id Thread id.
     * @apiSuccess (200) {String} data.forum_title Thread title.
     * @apiSuccess (200) {Integer} data.forum_comment_number The number of reply.
     * @apiSuccess (200) {Datetime} data.forum_timestamp The time of Thread publish.
     * @apiSuccess (200) {Integer} data.category_id The type id of category.
     * @apiSuccess (200) {String} data.category_name The type name of category.
     * @apiSuccess (200) {Integer} data.user_id User id of publisher.
     * @apiSuccess (200) {String} data.user_username User name of publisher.
     * @apiSuccess (200) {String} data.user_nickname User nickname of publisher.
     *
     * @apiError (400) (String) OUT_OF_RANGE Arguments out of range.
     */
    public function ac_list_get($category_id, $page, $limit)
    {
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 20;
        }
        if ($limit > 0 && $limit <= 20 && $page > 0) {
            $data = (new ForumModel())->getThreadTitle($category_id, $page, $limit);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }

    }

    /**
     * @param $forum_id
     * @param $page
     * @param $limit
     *
     * @api {get} /forum/reply/ Get list of reply
     * @apiName GetReply
     * @apiGroup Forum
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/forum/reply
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} forum_id The id of thread.
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.forum_comments_id Reply comment id.
     * @apiSuccess (200) {String} data.forum_comments The content of reply.
     * @apiSuccess (200) {Datetime} data.forum_comments_timestamp The time of reply publish.
     * @apiSuccess (200) {Integer} data.user_id The type id of user.
     * @apiSuccess (200) {String} data.user_username User name of publisher.
     * @apiSuccess (200) {String} data.user_nickname User nickname of publisher.
     * @apiSuccess (200) {String} data.user_avatar_url The url of user's avatar
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     */
    public function ac_reply_get($forum_id, $page, $limit)
    {
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 20;
        }
        if ($limit > 0 && $limit <= 20 && $page > 0) {
            $data = (new ForumReplyModel())->getReply($forum_id, $page, $limit);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }

    }

    /**
     * @param $forum_id
     *
     * @api {get} /forum/content/ Get content of thread
     * @apiName GetThread
     * @apiGroup Forum
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/forum/content
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} forum_id The thread id.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {String} data.forum_title Thread title.
     * @apiSuccess (200) {String} data.forum_content The content of thread.
     * @apiSuccess (200) {Datetime} data.forum_timestamp The time of thread publish.
     * @apiSuccess (200) {Integer} data.user_id User id of publisher.
     * @apiSuccess (200) {String} data.user_username User name of publisher.
     * @apiSuccess (200) {String} data.user_nickname User nickname of publisher.
     * @apiSuccess (200) {Integer} data.category_id The type id of category.
     * @apiSuccess (200) {String} data.category_name The type name of category.
     * @apiSuccess (200) {String} data.user_avatar_url The url of user's avatar
     *
     * @apiError (400) {String} INVALID_ARGUMENTS The operation can not be created.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_content_get($forum_id)
    {
        if (!empty($forum_id)) {
            if ($data = (new ForumModel())->getThreadContent($forum_id)) {
                $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
            } else {
                $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        }
    }
    /**
     * @param $page
     * @param $limit
     *
     * @api {get} /forum/category/ Get list of category
     * @apiName GetCategory
     * @apiGroup Forum
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/forum/category
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.category_id Category id.
     * @apiSuccess (200) {String} data.category_name The name of category
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     */
    public function ac_category_get($page, $limit)
    {
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 20;
        }
        if ($data = (new ForumCategoriesModel())->getForumCategory($page, $limit)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param $name
     * @filter admin canAddCategory
     *
     * @api {post} /forum/category/ Add category
     * @apiName AddCategory
     * @apiGroup Forum
     * @apiPermission admin
     * @apiSampleRequest https://dev.iecho.cc/api/forum/category
     * @apiVersion 0.1.0
     *
     * @apiParam {String} name The name of category.
     * @apiParam {String} user_token User access token.
     *
     * @apiSuccess (201) {String} CREATE Add success.
     *
     * @apiError (400) {String} INVALID_ARGUMENTS The operation can not be created.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_category_post($name)
    {
        if (!empty($name)) {
            if ((new ForumCategoriesModel())->addForumCategory($name)) {
                $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
            } else {
                $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canPostThread
     * @param $category_id
     * @param $forum_content
     * @param $forum_title
     *
     * @api {post} /forum/content/ Create thread
     * @apiName CreateThread
     * @apiGroup Forum
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/forum/content
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} category_id The id of category.
     * @apiParam {Integer} user_token User access token.
     * @apiParam {String} forum_content The content of thread.
     * @apiParam {String} forum_title The thread.
     *
     * @apiSuccess (201) {String} CREATE Add success.
     *
     * @apiError (400) {String} INVALID_CATEGORY_ID The category does not exist.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_content_post($category_id, $forum_content, $forum_title)
    {
        if ((new ForumModel())->timeDiffer()) {
            if ((new ForumCategoriesModel)->checkCategoryId($category_id)) {

                if ((new ForumModel())->addThread($category_id, $forum_content, $forum_title)) {
                    $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_CATEGORY_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canDeleteThread
     *
     * @api {patch} /forum/content/ Delete thread
     * @apiName DeleteThread
     * @apiGroup Forum
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/forum/content
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} forum_id The id of thread.
     * @apiParam {String} user_token User access token.
     *
     * @apiSuccess (200) {String} OK Delete success.
     *
     * @apiError (400) {String} INVALID_FORUM_ID The thread does not exist.
     * @apiError (400) {String} INVALID_ARGUMENTS The operation can not be created.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_content_patch()
    {
        $request = BunnyPHP:: getRequest();
        $request->process();
        $forum_id = $request['forum_id'];
        if ((new ForumModel())->timeDiffer()) {
            if ((new ForumModel)->checkThreadStatus($forum_id)) {
                if ((new ForumModel())->deleteThread($forum_id)) {
                    $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_FORUM_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canAddReply
     * @param $forum_id
     * @param $forum_comment
     * @param $forum_comment_status
     *
     * @api {post} /forum/reply/ Add reply
     * @apiName AddReply
     * @apiGroup Forum
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/forum/reply
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} forum_id The id of thread.
     * @apiParam {String} user_token User access token.
     * @apiParam {String} forum_comment The content of reply.
     *
     * @apiSuccess (201) {String} CREATE Add success.
     *
     * @apiError (400) {String} INVALID_FORUM_ID The thread does not exist.
     * @apiError (400) {String} INVALID_ARGUMENTS The operation can not be created.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_reply_post($forum_id, $forum_comment, $forum_comment_status)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ((new ForumModel())->timeDiffer()) {
            if ((new ForumModel())->checkThreadId($forum_id)) {
                if ((new ForumReplyModel())->addReply($forum_id, $user_id, $forum_comment, $forum_comment_status)) {
                    $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENT', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_FORUM_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canDeleteReply
     *
     * @api {patch} /forum/reply/ Delete reply
     * @apiName DeleteReply
     * @apiGroup Forum
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/forum/reply
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} forum_id The id of thread.
     * @apiParam {String} user_token User access token.
     * @apiParam {Integer} forum_comments_id The id of reply.
     *
     * @apiSuccess (200) {String} OK Delete success.
     *
     * @apiError (400) {String} INVALID_FORUM_ID The thread does not exist.
     * @apiError (400) {String} INVALID_ARGUMENTS The operation can not be achieve.
     * @apiError (429) {String} TOO_MANY_REQUEST Refresh too often.
     */
    public function ac_reply_patch()
    {
        $request = BunnyPHP:: getRequest();
        $request->process();
        $forum_id = $request['forum_id'];
        $forum_comments_id = $request['forum_comments_id'];
        if ((new ForumReplyModel())->timeDiffer()) {
            if ((new ForumModel())->checkThreadStatus($forum_id)) {
                if ((new ForumReplyModel())->deleteReply($forum_id, $forum_comments_id) != null) {
                    $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => ''])->render();
                } else {
                    $this->assignAll(['msg' => 'INVALID_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
                }
            } else {
                $this->assignAll(['msg' => 'INVALID_FORUM_ID', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'TOO_MANY_REQUEST', 'code' => 429, 'extra' => ''])->render();
        }
    }

}
