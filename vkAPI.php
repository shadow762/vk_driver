<?php

/**
 * Created by PhpStorm.
 * User: shado
 * Date: 02.07.2017
 * Time: 6:09
 */
class vkAPI
{
    public function __construct()
    {

    }

    /**
     * @brief Метод для получения участников группы.
     * @param null $groupId - id группы
     * @return object:
     *          ->count - количество
     *          ->items - array - данные
     */
    public function get_subscribers($groupId = null) {
        if (!$groupId)
            return [];

        $page = 0;
        $limit = 1000;
        $users = array();
        do {
            $offset = $page * $limit;
            //Получаем список пользователей
            $ch = curl_init("https://api.vk.com/method/groups.getMembers?group_id=$groupId&v=5.16&fields=sex,bdate,city,country,photo_200_orig,photo_max_orig&offset=$offset&count=$limit&access_token=" . VK_API_TOKEN);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $members = json_decode(curl_exec($ch));
            curl_close($ch);

            //Спим - макс 3 запроса в секунду
            usleep(333333);

            foreach($members->response->items as $user ) {
                $users []= $user; // добавляем юзера к юзерам
            }
            //Увеличиваем страницу
            $page++;
        } while($members->response->count > $offset + $limit );

        foreach ($users as $n => $user) // ходим по юзерам
            if(@$user->deactivated) // и забаненных
                unset($users[$n]); // удаляем

        return $users->response;
    }

    /**
     * @brief Метод для получения стран
     * @full Пример возврата:
     *   [count] => 236
     *   [items] => Array
     *   (
     *      [0] => stdClass Object
     *      (
     *          [id] => 19
     *          [title] => Австралия
     *      )
     *      [1] => stdClass Object
     *      (
     *         [id] => 20
     *         [title] => Австрия
     *      )
     * @return object:
     *          ->count - количество
     *          ->items - array - данные
     */
    public function get_countries() {
        $page = 0;
        $limit = 1000;

        $countries = [];
        do {
            $offset = $page * $limit;
            //Получаем список стран
            $ch = curl_init("https://api.vk.com/method/database.getCountries?need_all=1&v=5.16&offset=$offset&count=$limit&access_token=" . VK_API_TOKEN);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            if(!$page)
                $countries = json_decode(curl_exec($ch));
            else
                $countries->response->items = array_merge($countries->response->items, json_decode(curl_exec($ch))->response->items);

            curl_close($ch);

            //Спим - макс 3 запроса в секунду
            usleep(333333);

            //Увеличиваем страницу
            $page++;
        } while($countries->response->count > $offset + $limit );

        return $countries->response;
    }

    /**
     * @brief Метод для получения регионов
     * @full Пример возврата:
     *   [count] => 236
     *   [items] => Array
     *   (
     *      [0] => stdClass Object
     *      (
     *          [id] => 1000001
     *          [title] => Адыгея
     *      )
     *      [1] => stdClass Object
     *      (
     *         [id] => 1121540
     *         [title] => Алтай
     *      )
     * @param $country_id - id страны
     * @return object:
     *          ->count - количество
     *          ->items - array - данные
     */
    public function get_regions($country_id) {
        $page = 0;
        $limit = 1000;

        $regions = [];

        do {
            $offset = $page * $limit;
            //Получаем список стран
            $ch = curl_init("https://api.vk.com/method/database.getRegions?country_id=$country_id&v=5.16&offset=$offset&count=$limit&access_token=" . VK_API_TOKEN);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            if(!$page)
                $regions = json_decode(curl_exec($ch));
            else
                $regions->response->items = array_merge($regions->response->items, json_decode(curl_exec($ch))->response->items);

            curl_close($ch);

            //Спим - макс 3 запроса в секунду
            usleep(333333);

            //Увеличиваем страницу
            $page++;
        } while($regions->response->count > $offset + $limit );

        return $regions->response;
    }

    /**
     * @brief Метод для получения городов
     * @full Пример возврата:
     *   [count] => 159050
     *   [items] => Array
     *   (
     *      [0] => stdClass Object
     *      (
     *          [id] => 1
     *          [title] => Москва
     *          [important] => 1
     *      )
     *      [1] => stdClass Object
     *      (
     *         [id] => 2
     *         [title] => Санкт-Петербург
     *         [important] => 1
     *      )
     * @param $country_id - id страны
     * @param $region_id - id региона - необязательный
     * @return object:
     *          ->count - количество
     *          ->items - array - данные
     */
    public function get_towns($country_id, $region_id = null) {
        $page = 0;
        $limit = 1000;

        $towns = [];

        do {
            $offset = $page * $limit;
            //Получаем список стран
            $ch = curl_init("https://api.vk.com/method/database.getCities?country_id=$country_id&region_id=$region_id&need_all=1&v=5.16&offset=$offset&count=$limit&access_token=" . VK_API_TOKEN);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            if(!$page)
                $towns = json_decode(curl_exec($ch));
            else
                $towns->response->items = array_merge($towns->response->items, json_decode(curl_exec($ch))->response->items);

            curl_close($ch);

            //Спим - макс 3 запроса в секунду
            usleep(333333);

            //Увеличиваем страницу
            $page++;
        } while($towns->response->count > $offset + $limit );

        return $towns->response;
    }

    public function get_users_data(array $userIdList) {
        $chunkedArray = array_chunk($userIdList, 1000);

        foreach($chunkedArray as $key=>$item) {
            $users = implode(',', $item);

            $ch = curl_init("https://api.vk.com/method/users.get?user_ids=$users&fields=photo_id&v=5.16&access_token=" . VK_API_TOKEN);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            if(!$key)
                $users_data = json_decode(curl_exec($ch));
            else
                $users_data->response->items = array_merge($users_data->response->items, json_decode(curl_exec($ch))->response->items);

        }
        return $users_data->response;
    }

    public function search_users() {
        $params = [
            'location' => [
                'country_id' => null,
                'town_id' => null
            ],
            'sex' => 0, //1 - женский, 2 - мужской, 0 - любой
            'age_from' => null,
            'age_to' => null,
            'limit' => 5 //максимум за 1 запрос - 1000
        ];

        $page = 0;
        $limit = $params['limit'];

        $users = [];

            $offset = $page * $limit;
            //Получаем список стран
            $ch = curl_init("https://api.vk.com/method/users.search?country_id={$params['location']['country_id']}&v=5.16&count=$limit&access_token=" . VK_API_TOKEN);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $users = json_decode(curl_exec($ch));

            curl_close($ch);


        return $users->response;
    }
}