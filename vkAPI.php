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
     * @param null $groupId
     * @return array
     */
    public function getGroupMembers($groupId = null) {
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

            echo '<pre>';
            print_r($users);
            echo '</pre>';
    }
}