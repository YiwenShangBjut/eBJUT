<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/4/28
 * Time: 10:43
 */

class BooksTransactionController extends Controller
{

    /**
     * @param $book_category_id integer
     *
     * @api {get} /booksTransaction/category/ Request category details
     * @apiName GetCategory
     * @apiGroup Book Transaction
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/booksTransaction/category/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} book_category_id Book category id, 0 for all.
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.book_category_id Category id.
     * @apiSuccess (200) {String} data.book_category_name Category title.
     */
    public function ac_category($book_category_id)
    {
        $data = (new BooksCategoriesModel())->getCategory($book_category_id);
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
    }

    /**
     * @param int $page
     * @param int $limit
     * @param String $book_category
     * @param int $book_transaction_status
     *
     * @api {get} /booksTransaction/list/ Get Book Transaction list
     * @apiName GetBooksTransaction
     * @apiGroup Book Transaction
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/booksTransaction/list/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {String} book_category A connected string with category id, split by ','.
     * @apiParam {Integer} book_transaction_status the state type
     *
     * @apiSuccess (200) {String} OK Get success.
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.user_id Seller's id.
     * @apiSuccess (200) {String} data.user_nickname Seller's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url Seller's avatar.
     * @apiSuccess (200) {Integer} data.book_category_id The category id.
     * @apiSuccess (200) {String} data.book_category_name The category.
     * @apiSuccess (200) {Integer} data.book_transaction_id The id.
     * @apiSyccess (200) {String} data.book_transaction_title The title.
     * @apiSuccess (200) {String} data.book_transaction_detail The detail.
     * @apiSuccess (200) {Numeric} data.book_transaction_price The expected price
     * @apiSuccess (200) {String} data.book_transaction_com_type The communication ways.
     * @apiSuccess (200) {String} data.book_transaction_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.book_transaction_time Book transaction published time.
     * @apiSuccess (200) {Datetime} data.book_transaction_update_time The time when finish the transaction.
     *
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     * @apiError (400) {String} INVALID_AGREEMENT Status not accepted.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     */
    public function ac_list($page, $limit, $book_category, $book_transaction_status)
    {
        if (empty($page) || empty($limit) || empty($book_category) || (empty($book_transaction_status) && $book_transaction_status != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($limit < 0 && $limit > 20 && $page < 0) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($book_transaction_status != 0 && $book_transaction_status != 1 && $book_transaction_status != 3) {
            $this->assignAll(['msg' => 'INVALID_ARGUMENT', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new BooksTransactionModel())->getBookTransaction($page, $limit, $book_category, $book_transaction_status);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }

    /**
     * @filter auth canAddBookTransaction
     * @param $book_category
     * @param $book_transaction_title
     * @param $book_transaction_detail
     * @param $book_transaction_price
     * @param $book_transaction_com_type
     * @param $book_transaction_com_detail
     *
     * @api {post} /booksTransaction/record/ Publish transaction
     * @apiName AddBooksTransaction
     * @apiGroup Book Transaction
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/booksTransaction/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token The user access token
     * @apiParam {Integer} book_category The book category id
     * @apiParam {String} book_transaction_title The title of the information
     * @apiParam {String} book_transaction_detail The detail of the information
     * @apiParam {Numeric} boot_transaction_price The transaction expected price
     * @apiParam {Integer} book_transaction_com_type The communication way
     * @apiParam {String} book_transaction_com_detail The communication number
     *
     * @apiSuccess (201) {String} CREATED Add success.
     *
     * @apiError (406) {String} NOT_ACCEPTABLE Arguments access too frequent.
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (400) {String} INVALID_AGREEMENT Arguments invalid
     */
    public function ac_record_post($book_category, $book_transaction_title, $book_transaction_detail, $book_transaction_price, $book_transaction_com_type, $book_transaction_com_detail)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ($book_transaction_com_type == 0) {
            $book_transaction_com_detail = ((new UsersModel())->getUserPhoneByUserId($user_id));
        }

        if (empty($book_transaction_title) || empty($book_transaction_com_detail) || (empty($book_transaction_com_type) && $book_transaction_com_detail != '0') || (empty($book_transaction_price) && $book_transaction_price != '0')) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($book_transaction_com_type != 0 && $book_transaction_com_type != 1 && $book_transaction_com_type != 2 && $book_transaction_com_type != 3) {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new BooksTransactionModel())->checkBookTransaction($user_id, $book_transaction_title)) {
            $this->assignAll(['msg' => 'NOT_ACCEPTABLE', 'code' => 406, 'extra' => ''])->render();
        } elseif ((new BooksTransactionModel())->addBookTransaction($book_category, $user_id, $book_transaction_title, $book_transaction_detail, $book_transaction_price, $book_transaction_com_type, $book_transaction_com_detail)) {
            $this->assignAll(['msg' => 'CREATE', 'code' => 201, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canUpdateBookTransaction
     *
     * @api {patch} /booksTransaction/record/ Complete transaction
     * @apiName UpdateBooksTransaction
     * @apiGroup Book Transaction
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/booksTransaction/record/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token The user access token
     * @apiParam {Integer} book_transaction_id The information id
     * @apiParam {Integer} book_transaction_status Update or delete
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
        $book_transaction_id = $request['book_transaction_id'];
        $book_transaction_status = $request['book_transaction_status'];
        $user_id = (new TokensModel())->getUserIdByToken();

        if (empty($book_transaction_id) || empty($book_transaction_status)) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($book_transaction_status != 1 && $book_transaction_status != 2) {
            $this->assignAll(['msg' => 'INVALID_AGREEMENT', 'code' => 400, 'extra' => ''])->render();
        } elseif ((new BooksTransactionModel())->updateBookTransaction($book_transaction_id, $user_id, $book_transaction_status)) {
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
     * @api {get} /booksTransaction/search/ Search book
     * @apiName searchBooksTransaction
     * @apiGroup Book Transaction
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/booksTransaction/search/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     * @apiParam {Integer} keyword The keyword.
     *
     * @apiSuccess (200) {String} OK Search success.
     * @apiSuccess (200) {Object[]} Data Response payload.
     * @apiSuccess (200) {Integer} data.user_id Seller's name.
     * @apiSuccess (200) {String} data.user_nickname Seller's nickname.
     * @apiSuccess (200) {String} data.user_avatar_url Seller's avatar.
     * @apiSuccess (200) {Integer} data.book_category_id The category id.
     * @apiSuccess (200) {String} data.book_category_name The category.
     * @apiSyccess (200) {Integer} data.book_transaction_id The title.
     * @apiSyccess (200) {String} data.book_transaction_title The title.
     * @apiSuccess (200) {String} data.book_transaction_detail The detail.
     * @apiSuccess (200) {Numeric} data.book_transaction_price The expected price
     * @apiSuccess (200) {String} data.book_transaction_com_type The communication ways.
     * @apiSuccess (200) {String} data.book_transaction_com_detail The communication id.
     * @apiSuccess (200) {Datetime} data.book_transaction_time Book transaction published time.
     *
     * @apiError (400) {String} MISSING_MESSAGE Missing message.
     * @apiError (400) {String} OUT_OF_RANGE Limit < 0 or limit > 20 or page < 0.
     */
    public function ac_search_get($page, $limit, $keyword)
    {
        if (empty($keyword) || empty($page) || empty($limit)) {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        } elseif ($limit < 0 || $limit > 20 || $page < 0) {
            $this->assignAll(['msg' => 'MISSING_MESSAGE', 'code' => 400, 'extra' => ''])->render();
        } else {
            $data = (new BooksTransactionModel())->searchBookTransaction($page, $limit, $keyword);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        }
    }
}