<?php

// ネームスペースはファイルの置き場に応じて変更する
namespace Fujijun\LaravelEnhancePackage\Eloquent;


/**
 * Trait EloquentSwitchWhereTrait
 * @package Fujijun\LaravelEnhancePackage\Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder switchWhere($conditionKey, $conditions = [], $default = null)
 * @example
 *
 * namespace App;
 *
 * use Illuminate\Database\Eloquent\Model;
 *
 * class User extends Model {
 *     use
 *
 * 例えば、セレクトボックス＋テキストボックスの2つで構成されたユーザ検索フォームがあるとする。
 * このセレクトボックス[名前][住所][電話番号]があり、選択項目をテキストボックスの内容で検索するものとする。
 * SQLインジェクション対策の観点から、セレクトボックスのvalueにはカラム名を入れない設計とし、ユーザの入力内容をテーブル検索条件のカラム名に指定できないようにする。
 * ▼HTMLの例
 *  <select name='condition'>
 *      <option value="search_name">名前</option>
 *      <option value="search_addr">住所</option>
 *      <option value="search_tell_num">電話番号</option>
 *  </select>
 *  <input type="text" name="search_word" value="">
 *
 * 上記のように、リクエストパラメータとしてカラム名を指定させないようにすることで、意図しないSQLクエリが生成されることを防ぐことができる。
 * セレクトボックスの内容に対応したSQLクエリを生成するためには、以下のようなPHPコードになる。
 * ▼検索クエリの例１（配列形式でクエリを生成する場合）
 *  $request = request();
 *  $condition = $request->get('condition');
 *  $word = $request->get('word');
 *
 *  $findUsers = \App\User::switchWhere($condition, [
 *      'search_name' => function ($query) use ($word) {
 *          $query->where('name', $word);
 *      },
 *      'search_addr' => function ($query) use ($word) {
 *          $query->where('address', 'LIKE', '%' . $word . '%');
 *      },
 *      'search_search_tell_num' => function ($query) use ($word) {
 *          $query->where('phone_number', $word);
 *      },
 *  ])->get();
 *
 * ▼検索クエリの例２（クロージャでクエリを生成する場合）
 *  $findUsers = \App\User::switchWhere($conditions, ::switchWhere($key, function ($query, $valueOfKey) use ($val) {
 *      switch ($valueOfKey) {
 *          case 'searchId':
 *              $query->where('id', $val); break;
 *          case 'searchName':
 *              $query->where('name', 'LIKE', '%' . $val . '%'); break;
 *          case 'searchAddress':
 *              $query->where('addr', 'LIKE', '%' . $val); break;
 *          default:
 *              abort(404);
 *      })
 *  })->get();
 *
 */
trait EloquentSwitchWhereTrait
{

    /**
     * $conditionKeyをキーとする$conditionsの条件式を実行する
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $conditionKey 条件式のキー
     * @param array|callable $conditions 条件式を配列かクロージャで指定
     * @param null $default 条件に満たない場合の式（$conditionsがクロージャの場合は利用不可）
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSwitchWhere(\Illuminate\Database\Eloquent\Builder $query, $conditionKey, $conditions = [], $default = null)
    {
        if (is_null($conditionKey)) {
            // 条件指定なし
            // デフォルトの式を実行
            self::callDefaultWhere($query, $default);
        } else if(is_callable($conditions)) {
            // 条件指定あり(クロージャ形式)
            self::callSwitchHandler($query, $conditionKey, $conditions);
        } else {
            if (!is_array($conditions)) {
                // 条件式が配列でなければエラー
                throw new \InvalidArgumentException('conditions param is array require.');
            }

            // 条件指定あり(配列形式)
            if (isset($conditionKey) && array_key_exists($conditionKey, $conditions)) {
                // 条件をキーに持つ条件式がある場合は実行
                $whereClosure = $conditions[$conditionKey];
                if (!is_callable($whereClosure)) {
                    throw new \InvalidArgumentException('condition param is closure require.');
                }
                $whereClosure($query);
            } else {
                // 条件に合う条件式がない場合デフォルトの式を実行
                self::callDefaultWhere($query, $default);
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $val
     * @param callable $handler
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function callSwitchHandler(\Illuminate\Database\Eloquent\Builder $query, $val, callable $handler)
    {
        return $handler($query, $val);
    }

    /**
     * デフォルトの条件式があれば実行する
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Closure $default
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private final function callDefaultWhere(\Illuminate\Database\Eloquent\Builder &$query, $default)
    {
        if (!is_null($default)) {
            if (!is_callable($default)) {
                throw new \InvalidArgumentException('default param is closure require.');
            }
            $query = $default($query);
        }
        return $query;
    }
}