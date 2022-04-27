<?php declare(strict_types = 1);

namespace App\Managers;

use App\Db;

/**
 * Class UserManager
 * @package App\Managers
 */
class UserManager
{
    /**
     * UserManager constructor.
     * @param Db $db
     */
    public function __construct(private Db $db)
    {
    }

    /**
     * @param array $attributes
     * @return int|null
     */
    public function create(array $attributes): ?int
    {
        $query = sprintf(
            'INSERT INTO users (`name`, `year_of_birth`, `created`) VALUES ("%s",%s,"%s")',
            $this->db->getConnection()->real_escape_string($attributes['name']),
            $attributes['yearOfBirth'],
            \date('Y-m-d')
        );

        return true === $this->db->getConnection()->query($query)
            ? $this->db->getConnection()->insert_id
            : null;
    }

    /**
     * @param int $userId
     * @param array $attributes
     * @return int number of affected rows
     */
    public function update(int $userId, array $attributes): int
    {
        $query = sprintf(
            'UPDATE users SET `name` = "%s", `year_of_birth` = "%s", `updated` = "%s" WHERE id = %s',
            $this->db->getConnection()->real_escape_string($attributes['name']),
            $attributes['yearOfBirth'],
            \date('Y-m-d'),
            $userId
        );

        return true === $this->db->getConnection()->query($query)
            ? $this->db->getConnection()->affected_rows
            : 0;
    }

    /**
     * @param int $userId
     * @return array|null
     */
    public function get(int $userId): ?array
    {
        $query = sprintf('SELECT * FROM users WHERE id = %s', $userId);
        $result = $this->db->getConnection()->query($query);


        return $result->fetch_assoc();
    }
}
