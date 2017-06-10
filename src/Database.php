<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 11:48
 */

namespace SubtleFramework;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Events\Dispatcher;
use PDO;
use SubtleFramework\Config\ConfigInterface;
use SubtleFramework\Database\Logger;
use SubtleFramework\Exception\ConfigNotSatisfiedException;

class Database
{
    /**
     * Create connection to database with Illuminate Query Builder instead of Eloquent
     *
     * @param ConfigInterface $config should have such fields: [driver, host, port,
     * database, username, password, charset, collation, prefix]
     * @return Connection
     * @throws ConfigNotSatisfiedException
     */
    public static function makeConnection(ConfigInterface $config)
    {
        $dbConfig = $config->get();

        if (($diff = array_diff(['driver', 'host', 'port',
     'database', 'username', 'password', 'charset', 'collation', 'prefix'], array_keys($dbConfig))) !== []) {
            throw new ConfigNotSatisfiedException('Config keys ' . implode('|', $diff) . ' are not set');
        }

        $capsule = new Manager();
        $capsule->addConnection($dbConfig);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $connection = $capsule->getConnection();
        $connection->enableQueryLog();

        $dispatcher = new Dispatcher();
        $dispatcher->listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(PDO::FETCH_ASSOC);
        });

        $dbName = $dbConfig['database'];
        $dispatcher->listen(QueryExecuted::class, function ($event) use ($dbName) {
            $sql = str_replace("?", "'%s'", $event->sql);
            $query = vsprintf($sql, $event->bindings);
            $context = ['connection' => $dbName, 'sql' => $query, 'time' => $event->time];
            Logger::makeLogger()->info('query', $context);
            Log::info('query', $context);
        });
        $connection->setEventDispatcher($dispatcher);

        return $connection;
    }
}