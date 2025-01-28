<?php

require_once __DIR__ . '/DatabaseConnection.php';

/**
 * Player Session
 *
 * @class PlayerSession
 */
class PlayerSession
{
    /**
     * @return array
     */
    public function getFirstSessionData(): array
    {
        return (new DatabaseConnection())->query($this->getFirstSessionSql());
    }

    /**
     * @return string
     */
    protected function getFirstSessionSql(): string
    {
        return 'WITH '
            . $this->getEventDifferences() . ','
            . $this->getSessionIdentifiers() . ','
            . $this->getFirstSessions() . ','
            . $this->getAverageSessionLength() . '
                SELECT
                    registration_date,
                    avg_first_session_length
                FROM
                    average_session_length
                ORDER BY
                    registration_date;';
    }

    /**
     * @return string
     */
    protected function getEventDifferences(): string
    {
        return 'event_differences AS (
            SELECT
                player_id,
                event_time,
                registration_date,
                TIMESTAMPDIFF(MINUTE, LAG(event_time)
                  OVER (PARTITION BY player_id ORDER BY event_time), event_time) AS time_diff
            FROM
                player_events
        )';
    }

    /**
     * @return string
     */
    protected function getSessionIdentifiers(): string
    {
        return 'session_identifiers AS (
            SELECT
                player_id,
                event_time,
                registration_date,
                SUM(CASE WHEN time_diff > 10 OR time_diff IS NULL THEN 1 ELSE 0 END) 
                OVER (PARTITION BY player_id ORDER BY event_time) AS session_id
            FROM
                event_differences
        )';
    }

    /**
     * @return string
     */
    protected function getFirstSessions(): string
    {
        return 'first_sessions AS (
            SELECT
                player_id,
                MIN(event_time) AS session_start,
                MAX(event_time) AS session_end,
                registration_date,
                session_id,
                TIMESTAMPDIFF(MINUTE, MIN(event_time), MAX(event_time)) AS session_length
            FROM
                session_identifiers
            GROUP BY
                player_id, session_id, registration_date
        )';
    }

    /**
     * @return string
     */
    protected function getAverageSessionLength(): string
    {
        return 'average_session_length AS (
            SELECT
                registration_date,
                AVG(session_length) AS avg_first_session_length
            FROM (
                SELECT
                    player_id,
                    registration_date,
                    session_length
                FROM
                    first_sessions
                WHERE
                    session_id = 1
            ) t
            GROUP BY
                registration_date
        )';
    }
}
