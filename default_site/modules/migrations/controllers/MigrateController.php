<?php

namespace app\modules\migrations\controllers;

use app\modules\migrations\components\DbMigrator;
use Yii;
use yii\console\Exception;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use app\components\controllers\ConsoleController;
use app\helpers\ModuleHelper;
use app\modules\migrations\models\EngineDbMigration;

/**
 * Advanced migration tool.
 *
 * A migration means a set of persistent changes to the application environment
 * that is shared among different developers. For example, in an application
 * backed by a database, a migration may refer to a set of changes to
 * the database, such as creating a new table, adding a new table column.
 *
 * This command provides support for tracking the migration history, upgrading
 * or downloading with migrations, and creating new migration skeletons.
 *
 * The migration history is stored in a database table named
 * as [[migrationTable]]. The table will be automatically created the first time
 * this command is executed, if it does not exist. You may also manually
 * create it as follows:
 *
 * ~~~
 * CREATE TABLE migration (
 *     version varchar(180) PRIMARY KEY,
 *     version alias(180),
 *     apply_time int(11) unsigned NOT NULL
 * )
 * ~~~
 *
 * You may configure additional migration paths using the module param `migrationPaths`
 *
 * Below are some common usages of this command:
 *
 * ~~~
 * # creates a new migration named 'create_user_table'
 * yii migrate/create create_user_table
 *
 * # applies ALL new migrations
 * yii migrate
 *
 * # reverts the last applied migration
 * yii migrate/down
 * ~~~
 */
class MigrateController extends ConsoleController
{
    /**
     * The name of the dummy migration that marks the beginning of the whole migration history.
     */
    const BASE_MIGRATION = 'v000000_000000_000000_base';

    /**
     * @var string the default command action.
     */
    public $defaultAction = 'up';
    /**
     * @var string the directory storing the migration classes. This can be either
     * a path alias or a directory.
     */
    public $migrationPath = '@migrations/source';
    /**
     * @var array additional aliases of migration directories
     */
    public $migrationLookup = [];
    /**
     * @var boolean lookup all application migration paths
     */
    public $disableLookup = false;
    /**
     * @var boolean
     */
    public $disableHistoryTableCheck = false;
    /**
     * @var string the name of the table for keeping applied migration information.
     */
    public $migrationTable = '';

    /**
     * @var string the template file for generating new migrations.
     * This can be either a path alias (e.g. "@app/migrations/template.php")
     * or a file path.
     */
    public $templateFile = '@yii/views/migration.php';
    /**
     * @var boolean whether to execute the migration in an interactive mode.
     */
    public $interactive = true;
    /**
     * @var boolean silent mode, minimum output
     */
    public $quiet = false;
    /**
     * @var Connection|string the DB connection object or the application
     * component ID of the DB connection.
     */
    public $db = 'db';

    /** @var array */
    protected $migration_history = null;

    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        return array_merge(
            parent::options($actionId),
            ['migrationPath', 'migrationLookup', 'disableLookup',
                'migrationTable', 'db', 'quiet', 'enableBuffer', 'disableHistoryTableCheck'], // global for all actions
            ($actionId == 'create') ? ['templateFile'] : [] // action create
        );
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * It checks the existence of the [[migrationPath]].
     *
     * @param \yii\base\Action $action the action to be executed.
     *
     * @throws Exception if db component isn't configured
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->migrationTable = EngineDbMigration::tableName();
        $path = Yii::getAlias($this->migrationPath);

        if ($action->id !== 'create') {
            if (is_string($this->db)) {
                $this->db = Yii::$app->get($this->db);
            }
            if (!$this->db instanceof Connection) {
                throw new Exception(
                    "The 'db' option must refer to the application component ID of a DB connection."
                );
            }
        } elseif (!is_dir($path)) {
            $this->stdout("\n$path does not exist, creating...");
            FileHelper::createDirectory($path);
        }

        $this->stdout("Yii Migration Tool (based on Yii v2.0.6)\n", Console::BOLD);

        if (isset($this->db->dsn)) {
            $this->stdout("Database Connection: " . $this->db->dsn . "\n", Console::FG_BLUE);
        }

        return true;
    }

    public function afterAction($action, $result)
    {
        if ($this->enableBuffer) {
            $this->flush();
        }
        return parent::afterAction($action, $result);
    }

    /**
     * Upgrades the application by applying new migrations.
     * For example,
     *
     * ~~~
     * yii migrate     # apply all new migrations
     * yii migrate 3   # apply the first 3 new migrations
     * ~~~
     *
     * @param integer $limit the number of new migrations to be applied. If 0, it means
     * applying all available new migrations.
     * @param bool    $quietLookup perform lookup without info messages (only errors and warnings)
     */
    public function actionUp($limit = 0, $quietLookup = false)
    {
        $migrations = $this->getNewMigrations($quietLookup);

        if (empty($migrations)) {
            $this->stdout("No new migration found. Your system is up-to-date.\n");
            return;
        }

        $total = count($migrations);
        $limit = (int)$limit;
        if ($limit > 0) {
            $migrations = array_slice($migrations, 0, $limit);
        }

        $this->stdout(($n = count($migrations)) === $total
            ? "Total $n new " . ($n === 1 ? 'migration' : 'migrations') . " to be applied:\n"
            : "Total $n out of $total new " . ($total === 1 ? 'migration' : 'migrations') . " to be applied:\n");

        $this->stdout("\nMigrations:\n");
        foreach ($migrations as $migration => $alias) {
            $this->stdout("    " . $migration . " (" . $alias . ")\n");
        }

        if ($this->enableBuffer) {
            $this->flush();
            $this->enableBuffer = false;
        }

        if ($this->confirm('Apply the above ' . ($n === 1 ? 'migration' : 'migrations') . "?")) {
            foreach ($migrations as $migration => $alias) {
                if (!$this->migrateUp($migration, $alias)) {
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n");

                    return;
                }
            }
            $this->stdout("\nMigrated up successfully.\n", Console::FG_GREEN);
        }
    }

    /**
     * Downgrades the application by reverting old migrations.
     * For example,
     *
     * ~~~
     * yii migrate/down     # revert the last migration
     * yii migrate/down 3   # revert the last 3 migrations
     * ~~~
     *
     * @param integer $limit the number of migrations to be reverted. Defaults to 1,
     * meaning the last applied migration will be reverted.
     *
     * @throws Exception if the number of the steps specified is less than 1.
     */
    public function actionDown($limit = 1)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            throw new Exception("The step argument must be greater than 0.");
        }

        $migrations = $this->getMigrationHistory($limit);
        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n");

            return;
        }

        $n = count($migrations);
        $this->stdout("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted:\n");
        foreach ($migrations as $migration => $info) {
            $this->stdout("    $migration (" . $info['alias'] . ")\n");
        }
        $this->stdout("\n");

        if ($this->confirm('Revert the above ' . ($n === 1 ? 'migration' : 'migrations') . "?")) {
            foreach ($migrations as $migration => $info) {
                if (!$this->migrateDown($migration, $info['alias'])) {
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n");

                    return;
                }
            }
            $this->stdout("\nMigrated down successfully.\n");
        }
    }

    /**
     * Redoes the last few migrations.
     *
     * This command will first revert the specified migrations, and then apply
     * them again. For example,
     *
     * ~~~
     * yii migrate/redo     # redo the last applied migration
     * yii migrate/redo 3   # redo the last 3 applied migrations
     * ~~~
     *
     * @param integer $limit the number of migrations to be redone. Defaults to 1,
     * meaning the last applied migration will be redone.
     *
     * @throws Exception if the number of the steps specified is less than 1.
     */
    public function actionRedo($limit = 1)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            throw new Exception("The step argument must be greater than 0.");
        }

        $migrations = $this->getMigrationHistory($limit);
        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n");

            return;
        }

        $n = count($migrations);
        $this->stdout("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be redone:\n");
        foreach ($migrations as $migration => $info) {
            $this->stdout("    $migration\n");
        }
        $this->stdout("\n");

        if ($this->confirm('Redo the above ' . ($n === 1 ? 'migration' : 'migrations') . "?")) {
            foreach ($migrations as $migration => $info) {
                if (!$this->migrateDown($migration, $info['alias'])) {
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n");

                    return;
                }
            }
            foreach (array_reverse($migrations) as $migration => $info) {
                if (!$this->migrateUp($migration, $info['alias'])) {
                    $this->stdout("\nMigration failed. The rest of the migrations migrations are canceled.\n");

                    return;
                }
            }
            $this->stdout("\nMigration redone successfully.\n");
        }
    }

    /**
     * Upgrades or downgrades till the specified version.
     *
     * Can also downgrade versions to the certain apply time in the past by providing
     * a UNIX timestamp or a string parseable by the strtotime() function. This means
     * that all the versions applied after the specified certain time would be reverted.
     *
     * This command will first revert the specified migrations, and then apply
     * them again. For example,
     *
     * ~~~
     * yii migrate/to 101129_185401                      # using timestamp
     * yii migrate/to m101129_185401_create_user_table   # using full name
     * yii migrate/to 1392853618                         # using UNIX timestamp
     * yii migrate/to "2014-02-15 13:00:50"              # using strtotime() parseable string
     * ~~~
     *
     * @param string $version either the version name or the certain time value in the past
     * that the application should be migrated to. This can be either the timestamp,
     * the full name of the migration, the UNIX timestamp, or the parseable datetime
     * string.
     *
     * @throws Exception if the version argument is invalid.
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function actionTo($version)
    {
        if (preg_match(DbMigrator::DB_VERSION_FULL_PATTERN, $version, $matches)) {
            $this->migrateToVersion('v' . $matches[1]);
        } elseif (preg_match(DbMigrator::DB_VERSION_WITH_DATE_PATTERN, $version, $matches)) {
            $this->migrateToVersion('v' . $matches[1], false);
        } elseif (preg_match(DbMigrator::DB_VERSION_ONLY_PATTERN, $version, $matches)) {
            $this->migrateToVersion('v' . $matches[1], false);
        } elseif ((string)(int)$version == $version) {
            $this->migrateToTime($version);
        } elseif (($time = strtotime($version)) !== false) {
            $this->migrateToTime($time);
        } else {
            throw new Exception("The version argument must be either a timestamp (e.g. 101129 or 101129_185401),\n"
                . " the full name of a migration (e.g. m200000_101129_185401_create_user_table),\n"
                . " a UNIX timestamp (e.g. 1392853000),"
                . " or a datetime string parseable\nby the strtotime() function (e.g. 2014-02-15 13:00:50).");
        }
    }

    /**
     * Modifies the migration history to the specified version.
     *
     * No actual migration will be performed.
     *
     * ~~~
     * yii migrate/mark 101129_185401                      # using timestamp
     * yii migrate/mark m101129_185401_create_user_table   # using full name
     * ~~~
     *
     * @param string $version the version at which the migration history should be marked.
     * This can be either the timestamp or the full name of the migration.
     *
     * @throws Exception if the version argument is invalid or the version cannot be found.
     */
    public function actionMark($version)
    {
        $originalVersion = $version;
        if (!preg_match(DbMigrator::DB_VERSION_FULL_PATTERN, $version, $matches)) {
            throw new Exception(
                "The version argument must be either a timestamp (e.g. 200000_101129_185401)\n"
                    . 'or the full name of a migration (e.g. v200000_101129_185401_create_user_table).'
            );
        }
        $version = 'v' . $matches[1];

        // try mark up
        $migrations = $this->getNewMigrations();
        foreach ($migrations as $migration => $alias) {
            $stack[$migration] = $alias;
            if (strpos($migration, $version . '_') === 0) {
                if ($this->confirm("Set migration history at $originalVersion?")) {
                    $command = $this->db->createCommand();
                    foreach ($stack as $applyMigration => $applyAlias) {
                        $command->insert(
                            $this->migrationTable,
                            [
                                'version' => $applyMigration,
                                'alias' => $applyAlias,
                                'apply_time' => time(),
                            ]
                        )->execute();
                    }
                    $this->stdout("The migration history is set at $originalVersion.\n"
                        . "No actual migration was performed.\n");
                }

                return;
            }
        }

        // try mark down
        $migrations = array_keys($this->getMigrationHistory(-1));
        foreach ($migrations as $upIndex => $migration) {
            if (strpos($migration, $version . '_') !== 0) {
                continue;
            }

            if ($upIndex === 0) {
                $this->stdout("Already at '$originalVersion'. Nothing needs to be done.\n");
                return;
            }

            if ($this->confirm("Set migration history at $originalVersion?")) {
                $command = $this->db->createCommand();
                for ($downIndex = 0; $downIndex < $upIndex; ++$downIndex) {
                    $command->delete($this->migrationTable, ['version' => $migrations[$downIndex]])->execute();
                }
                $this->stdout("The migration history is set at $originalVersion.\n"
                    . "No actual migration was performed.\n");
            }

            return;
        }

        throw new Exception("Unable to find the version '$originalVersion'.");
    }

    /**
     * Displays the migration history.
     *
     * This command will show the list of migrations that have been applied
     * so far. For example,
     *
     * ~~~
     * yii migrate/history     # showing the last 10 migrations
     * yii migrate/history 5   # showing the last 5 migrations
     * yii migrate/history 0   # showing the whole history
     * ~~~
     *
     * @param integer $limit the maximum number of migrations to be displayed.
     * If it is 0, the whole migration history will be displayed.
     */
    public function actionHistory($limit = 10)
    {
        $limit = (int)$limit;
        $migrations = $this->getMigrationHistory($limit);

        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n");
            return;
        }

        $n = count($migrations);

        $this->stdout($limit > 0
                ? "Showing the last $n applied " . ($n === 1 ? 'migration' : 'migrations') . ":\n"
                : "Total $n " . ($n === 1 ? 'migration has' : 'migrations have') . " been applied before:\n");

        foreach ($migrations as $version => $info) {
            $this->stdout("    (" . date('Y-m-d H:i:s', $info['apply_time']) . ') ' . $version . "\n");
        }
    }

    /**
     * Ensure what migration history table exists in database.
     *
     * This command will check existance of migration history table in
     * database and if it doesn't exists - create it.
     */
    public function actionInit()
    {
        if ($this->disableHistoryTableCheck) {
            return;
        }

        Yii::trace('Checking migration history table in database...', __METHOD__);

        if ($this->db->schema->getTableSchema($this->migrationTable, true) === null) {
            $this->actionHistoryCreate();
            return;
        }

        Yii::info('Migration history table exists in database', __METHOD__);
    }

    public function actionHistoryCreate()
    {
        $this->createMigrationHistoryTable();
    }

    /**
     * Displays the un-applied new migrations.
     *
     * This command will show the new migrations that have not been applied.
     * For example,
     *
     * ~~~
     * yii migrate/new     # showing the first 10 new migrations
     * yii migrate/new 5   # showing the first 5 new migrations
     * yii migrate/new 0   # showing all new migrations
     * ~~~
     *
     * @param integer $limit the maximum number of new migrations to be displayed.
     * If it is 0, all available new migrations will be displayed.
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function actionNew($limit = 10)
    {
        $limit = (int)$limit;
        $migrations = $this->getNewMigrations();

        if (empty($migrations)) {
            $this->stdout("No new migrations found. Your system is up-to-date.\n");
            return;
        }

        $n = count($migrations);

        if ($limit > 0 && $n > $limit) {
            $migrations = array_slice($migrations, 0, $limit);
            $this->stdout("Showing $limit out of $n new " . ($n === 1 ? 'migration' : 'migrations') . ":\n");
        } else {
            $this->stdout("Found $n new " . ($n === 1 ? 'migration' : 'migrations') . ":\n");
        }

        foreach ($migrations as $migration => $alias) {
            $this->stdout("    " . $migration . " (" . $alias . ")" . "\n");
        }
    }

    /**
     * Creates a new migration.
     *
     * This command creates a new migration using the available migration template.
     * After using this command, developers should modify the created migration
     * skeleton by filling up the actual migration logic.
     *
     * ~~~
     * yii migrate/create create_user_table
     * ~~~
     *
     * @param string $name the name of the new migration. This should only contain
     * letters, digits and/or underscores.
     *
     * @throws Exception if the name argument is invalid.
     */
    public function actionCreate($name)
    {
        if (!preg_match('/^\w+$/', $name)) {
            throw new Exception("The migration name should contain letters, digits and/or underscore characters only.");
        }

        $version = $this->module->dbMigrator->getDbVersion() . '_';
        $name = 'v' . $version . gmdate('ymd_His') . '_' . $name;
        $file = Yii::getAlias($this->migrationPath) . DIRECTORY_SEPARATOR . $name . '.php';

        if ($this->confirm("Create new migration '$file'?")) {
            $content = $this->renderFile(Yii::getAlias($this->templateFile), ['className' => $name]);
            file_put_contents(Yii::getAlias($file), $content);
            $this->stdout("New migration created successfully.\n");
        }
    }

    /**
     * Upgrades with the specified migration class.
     *
     * @param string $class the migration class name
     *
     * @return boolean whether the migration is successful
     */
    protected function migrateUp($class, $alias)
    {
        if ($class === self::BASE_MIGRATION) {
            return true;
        }

        $this->stdout("*** applying $class\n");
        $start = microtime(true);
        $migration = $this->createMigration($class, $alias);

        if ($migration->up() !== false) {
            $this->db->createCommand()->insert(
                $this->migrationTable,
                [
                    'version' => $class,
                    'alias' => $alias,
                    'apply_time' => time(),
                ]
            )->execute();
            $time = microtime(true) - $start;
            $this->stdout("*** applied $class (time: " . sprintf("%.3f", $time) . "s)\n\n");

            return true;
        }

        $time = microtime(true) - $start;
        $this->stdout("*** failed to apply $class (time: " . sprintf("%.3f", $time) . "s)\n\n");

        return false;
    }

    /**
     * Downgrades with the specified migration class.
     *
     * @param string $class the migration class name
     *
     * @return boolean whether the migration is successful
     */
    protected function migrateDown($class, $alias)
    {
        if ($class === self::BASE_MIGRATION) {
            return true;
        }

        $this->stdout("*** reverting $class\n");
        $start = microtime(true);
        $migration = $this->createMigration($class, $alias);
        if ($migration->down() !== false) {
            $this->db->createCommand()->delete(
                $this->migrationTable,
                [
                    'version' => $class,
                ]
            )->execute();
            $time = microtime(true) - $start;
            $this->stdout("*** reverted $class (time: " . sprintf("%.3f", $time) . "s)\n\n");

            return true;
        } else {
            $time = microtime(true) - $start;
            $this->stdout("*** failed to revert $class (time: " . sprintf("%.3f", $time) . "s)\n\n");

            return false;
        }
    }

    /**
     * Creates a new migration instance.
     *
     * @param string $class the migration class name
     *
     * @return \yii\db\Migration the migration instance
     */
    protected function createMigration($class, $alias)
    {
        $file = $class . '.php';
        require_once(\Yii::getAlias($alias) . '/' . $file);

        return new $class(['db' => $this->db]);
    }

    /**
     * Migrates to the specified apply time in the past.
     *
     * @param integer $time UNIX timestamp value.
     */
    protected function migrateToTime($time)
    {
        $count = 0;
        $migrations = array_values($this->getMigrationHistory(-1));

        while ($count < count($migrations) && $migrations[$count] > $time) {
            ++$count;
        }

        if ($count === 0) {
            $this->stdout("Nothing needs to be done.\n");
            return;
        }

        $this->actionDown($count);
    }

    /**
     * Migrates to the certain version.
     *
     * @param string $version name in the full format.
     * @param $strictCompare
     *
     * @throws Exception if the provided version cannot be found.
     */
    protected function migrateToVersion($version, $strictCompare = true)
    {
        list($limit, $exists) = $this->isDowngradeNeededTo($version);

        if ($exists) {
            if ($limit > 0) {
                $this->actionDown($limit);
            } else {
                $this->stdout("Already at '$version'.\n", Console::FG_GREEN);
                $this->stdout("No changes needs to be applied.\n");
            }
            if ($strictCompare) {
                return;
            }
        } else {
            $this->stdout("No changes needs to be applied.\n");
        }

        list($limit, $exists) = $this->isUpgradeNeededTo($version, $strictCompare);

        if ($exists) {
            $this->actionUp($limit, true);
        } else {
            if ($strictCompare) {
                $this->stdout("Version '$version' not found!\n", Console::FG_RED);
            } else {
                $this->stdout("No changes needs to be applied.\n");
            }
        }
    }

    protected function isDowngradeNeededTo($version)
    {
        $this->stdout("Search migrations for downgrading...\n");

        $appliedMigrations = array_keys($this->getMigrationHistory(-1));
        $found = false;
        $limit = 0;

        foreach ($appliedMigrations as $appliedMigration) {
            if (strpos($appliedMigration, $version . '_') === 0) {
                $found = true;
                break;
            }
            ++$limit;
        }

        return [$limit, $found];
    }

    protected function isUpgradeNeededTo($version, $strictCompare = true)
    {
        $this->stdout("Search migrations for upgrading...\n");

        $migrations = $this->getNewMigrations();
        $found = false;
        $limit = 0;

        if ($strictCompare) {
            foreach ($migrations as $migration => $alias) {
                ++$limit;
                if (strpos($migration, $version . '_') === 0) {
                    $found = true;
                    break;
                }
            }
        } else {

            $versionNumber = floatval(preg_replace('/[^\d]/', '', str_pad(substr($version, 0, 13), 12, '0')));

            foreach ($migrations as $migration => $alias) {

                if (strpos($migration, $version . '_') === 0
                    || floatval(preg_replace('/[^\d]/', '', substr($migration, 0, 13))) < $versionNumber)
                {
                    ++$limit;
                    $found = true;
                } else {
                    break;
                }
            }
        }

        return [$limit, $found];
    }

    /**
     * Returns the migration history.
     *
     * @param integer $limit the maximum number of records in the history to be returned
     *
     * @return array the migration history
     */
    protected function getMigrationHistory($limit = -1)
    {
        if (is_null($this->migration_history)) {
            $this->actionInit();
            $query = new Query;
            $rows = $query->select(['version', 'alias', 'apply_time'])
                ->from($this->migrationTable)
                ->orderBy('apply_time DESC, version DESC')
                ->createCommand($this->db)
                ->queryAll();
            $history = ArrayHelper::map($rows, 'version', 'apply_time');
            foreach ($rows as $row) {
                $history[$row['version']] = ['apply_time' => $row['apply_time'], 'alias' => $row['alias']];
            }

            unset($history[self::BASE_MIGRATION]);
            $this->migration_history = $history;
        }

        return $limit > 0
            ? array_slice($this->migration_history, 0, $limit)
            : $this->migration_history;
    }

    /**
     * Creates the migration history table.
     */
    protected function createMigrationHistoryTable()
    {
        Yii::trace('Creating migration history table \'' . $this->migrationTable . '\'', __METHOD__);
        $tableName = $this->db->schema->getRawTableName($this->migrationTable);
        $this->stdout("Creating migration history table \"$tableName\"...");
        $this->db->createCommand()->createTable(
            $this->migrationTable,
            [
                'version' => 'varchar(180) NOT NULL PRIMARY KEY',
                'alias' => 'varchar(180) NOT NULL',
                'apply_time' => 'int(11) unsigned NOT NULL',
            ]
        )->execute();
        $this->db->createCommand()->insert(
            $this->migrationTable,
            [
                'version' => self::BASE_MIGRATION,
                'alias' => $this->migrationPath,
                'apply_time' => time(),
            ]
        )->execute();
        $this->stdout("done.\n");
    }

    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations, (key: migration version; value: alias)
     */
    protected function getNewMigrations($quiet = false)
    {
        $applied = [];
        foreach ($this->getMigrationHistory(-1) as $version => $info) {
            $applied[substr($version, 1, 20)] = true;
        }

        $module = Yii::$app->getModule(ModuleHelper::MIGRATIONS);

        if (isset($module->migrationPaths)) {
            $this->migrationLookup = ArrayHelper::merge($this->migrationLookup, $module->migrationPaths);
        }

        $directories = $this->migrationPath && $this->disableLookup
            ? [$this->migrationPath]
            : ArrayHelper::merge([$this->migrationPath], $this->migrationLookup);
        $migrations = [];

        if (!$quiet) {
            $this->stdout("\nLookup:\n");
        }

        foreach ($directories as $alias) {
            $dir = Yii::getAlias($alias);
            if (!is_dir($dir)) {
                $label = $this->ansiFormat('[warn]', Console::BG_YELLOW);
                $this->stdout(" {$label}  " . $alias . " (" . \Yii::getAlias($alias) . ")\n");
                Yii::warning("Migration lookup directory '{$alias}' not found", __METHOD__);
                continue;
            }
            $handle = opendir($dir);
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(v(\d{6}_\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($path)
                    && !isset($applied[$matches[2]])
                ) {
                    $migrations[$matches[1]] = $alias;
                }
            }
            closedir($handle);
            if (!$quiet) {
                $label = $this->ansiFormat('[ok]', Console::FG_GREEN);
                $this->stdout(" {$label}    " . $alias . " (" . \Yii::getAlias($alias) . ")\n");
            }
        }
        ksort($migrations);

        $this->stdout("\n");

        return $migrations;
    }
}
