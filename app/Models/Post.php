<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'M_POST';

    /**
     * Search for a post by postcode.
     *
     * @param string $_param
     * @return array|null
     */
    public function searchPostCord($_param)
    {
        $result = [];

        if ($_param) {
            $result = $this->where('POSTCODE', $_param)->first();
        }

        if ($result) {
            $result->POSTCODE1 = substr($result->POSTCODE, 0, 3);
            $result->POSTCODE2 = substr($result->POSTCODE, 3, 4);
        }

        return $result ? $result->toArray() : null;
    }

    /**
     * Search for post codes based on POSTCODE1 and POSTCODE2.
     *
     * @param array $_param
     * @return array|null
     */
    public function candidacyPostCord($_param)
    {
        $data = $this->searchArray($_param, ['POSTCODE1', 'POSTCODE2']);

        $result = [];

        $postLength = mb_strlen($data['POSTCODE1']) + mb_strlen($data['POSTCODE2']);

        if ($data['POSTCODE1'] && $data['POSTCODE2'] && $postLength == 7) {
            $result = $this->where('POSTCODE', 'LIKE', $data['POSTCODE1'] . $data['POSTCODE2'] . '%')
                           ->limit(5)
                           ->get();
        }

        if ($result->isNotEmpty()) {
            $county = config('prefecture_code');
            $result = $result->map(function ($item) use ($county) {
                $item['COUNTY'] = $county[$item->CNT_ID];
                return $item;
            })->toArray();
            return $result;
        }

        return null;
    }

    /**
     * Mock implementation of SearchArray method.
     *
     * @param array $_param
     * @param array $fields
     * @return array
     */
    private function searchArray($_param, $fields)
    {
        // Mock implementation of searchArray function
        return array_intersect_key($_param, array_flip($fields));
    }
}

