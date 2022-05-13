<?php
namespace Go2Flow\SaasRegisterLogin\Models;

use Go2Flow\SaasRegisterLogin\Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Team extends \Go2Flow\SaasRegisterLogin\Models\AbstractModels\AbstractTeam  implements HasMedia
{
    use HasFactory, HasApiTokens, InteractsWithMedia;

    const MEDIA_LOGO = 'logo';
    const MEDIA_CONVERSION_LOGO = 'logo';

    /** @return TeamFactory */
    protected static function newFactory()
    {
        return TeamFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::MEDIA_LOGO)
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion(self::MEDIA_CONVERSION_LOGO)
            ->width(250)
            ->height(250)
            ->nonQueued()->performOnCollections(self::MEDIA_LOGO);
    }
}
