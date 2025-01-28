<?php

/**
 * Response Preparer
 *
 * @class ResponsePreparer
 */
class ResponsePreparer
{
    /**
     * @param array $results
     * @return string
     */
    public static function prepare(array $results): string
    {
        $x = array_column($results, 'registration_date');
        $y = array_column($results, 'avg_first_session_length');

        return json_encode([
            'x_axis' => $x,
            'y_axis' => $y,
        ]);
    }
}
