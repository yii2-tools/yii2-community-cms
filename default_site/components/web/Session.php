<?php

namespace app\components\web;

use Yii;
use yii\web\Session as BaseSession;
use yii\web\DbSession;
use yii\db\Query;
use yii\db\Expression;

/**
 * Class Session
 * @package app\components\web
 *
 * DO NOT CODE IN THIS CLASS
 * THIS CLASS USED ONLY IN DEV ENVIRONMENT
 * OR IN PROD, IF REDIS IS DOWN
 */
class Session extends DbSession
{
    public $sessionTable = '{{%users_sessions}}';

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function regenerateID($deleteOldSession = false)
    {
        $oldID = session_id();

        // if no session is started, there is nothing to regenerate
        if (empty($oldID)) {
            return;
        }

        BaseSession::regenerateID(false);
        $newID = session_id();

        $query = new Query();
        $row = $query->from($this->sessionTable)
            ->where('[[id]]=UNHEX(:id)', [':id' => $oldID])
            ->createCommand($this->db)
            ->queryOne();

        if ($row !== false) {
            if ($deleteOldSession) {
                $fields = ['id' => new Expression('UNHEX(:id)', [':id' => $newID])];
                $this->db->createCommand()
                    ->update($this->sessionTable, $fields, '[[id]]=UNHEX(:id)', [':id' => $oldID])
                    ->execute();

                return;
            }

            $row['id'] = new Expression('UNHEX(:id)', [':id' => $newID]);
            $this->db->createCommand()
                ->insert($this->sessionTable, $row)
                ->execute();

            return;
        }

        // shouldn't reach here normally
        $this->db->createCommand()
            ->insert($this->sessionTable, $this->composeFields(
                new Expression('UNHEX(:id)', [':id' => $newID]),
                ''
            ))
            ->execute();
    }

    public function readSession($id)
    {
        $query = new Query();
        $query->from($this->sessionTable)
            ->where('[[expire]]>:expire AND [[id]]=UNHEX(:id)', [':expire' => time(), ':id' => $id]);

        if (isset($this->readCallback)) {
            $fields = $query->one($this->db);
            return $fields === false ? '' : $this->extractData($fields);
        }

        $data = $query->select(['data'])->scalar($this->db);
        return $data === false ? '' : $data;
    }

    public function writeSession($id, $data)
    {
        // exception must be caught in session write handler
        // http://us.php.net/manual/en/function.session-set-save-handler.php
        try {
            $query = new Query;
            $exists = $query->select(['id'])
                ->from($this->sessionTable)
                ->where('[[id]]=UNHEX(:id)', [':id' => $id])
                ->createCommand($this->db)
                ->queryScalar();
            $fields = $this->composeFields($id, $data);
            if ($exists === false) {
                $fields['id'] = new Expression('UNHEX(:id)', [':id' => $id]);
                $this->db->createCommand()
                    ->insert($this->sessionTable, $fields)
                    ->execute();
            } else {
                unset($fields['id']);
                $this->db->createCommand()
                    ->update($this->sessionTable, $fields, '[[id]]=UNHEX(:id)', [':id' => $id])
                    ->execute();
            }
        } catch (\Exception $e) {
            $exception = ErrorHandler::convertExceptionToString($e);
            // its too late to use Yii logging here
            error_log($exception);
            echo $exception;

            return false;
        }

        return true;
    }

    public function destroySession($id)
    {
        $this->db->createCommand()
            ->delete($this->sessionTable, '[[id]]=UNHEX(:id)', [':id' => $id])
            ->execute();

        return true;
    }
}
