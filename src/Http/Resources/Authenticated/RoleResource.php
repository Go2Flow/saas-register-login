<?php

namespace Go2Flow\SaasRegisterLogin\Http\Resources\Authenticated;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    private $unsettables = ['pivot'];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $this->unsetData($data);
        return $data;
    }

    private function unsetData(array &$data)
    {
        foreach ($this->unsettables as $unsettable) {
            unset($data[$unsettable]);
        }
    }
}
