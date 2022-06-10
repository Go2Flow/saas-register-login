<?php

namespace Go2Flow\SaasRegisterLogin\Http\Resources\Authenticated;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

class UserResource extends JsonResource
{
    private $unsettables = ['pivot', 'roles'];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $roles = $this->whenLoaded('roles');
        $data['role'] = new RoleResource($roles->isMissing()?$roles:$roles->first());
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
