<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles,
        SoftDeletes,
        HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'active',
        'password',
        'slug',
        'titre_pro',
        'bio',
        'photo',
        'est_medecin',
        'profil_public',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** Articles relus par ce compte (médecin relecteur) */
    public function relectures(): HasMany
    {
        return $this->hasMany(Post::class, 'reviewer_id');
    }

    /** Adresse de la page auteur, créée automatiquement à partir du nom */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if (!$user->slug) {
                $base = Str::slug(Str::before($user->name, '@')) ?: 'auteur';
                $slug = $base;
                $i = 2;
                while (static::withTrashed()->where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $user->slug = $slug;
            }
        });
    }

    /** Nom affiché sur le site (sans la partie technique « @… » des identifiants) */
    public function getNomPublicAttribute(): string
    {
        return Str::before($this->name, '@') ?: $this->name;
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? Storage::disk('public')->url($this->photo) : null;
    }

    public function initiales(): string
    {
        $nom  = preg_replace('/^(dr|pr|docteur|professeur)\.?\s+/iu', '', trim($this->nom_public));
        $mots = preg_split('/\s+/u', $nom);

        return mb_strtoupper(mb_substr($mots[0] ?? '', 0, 1) . mb_substr($mots[1] ?? '', 0, 1)) ?: 'A';
    }







    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'est_medecin'       => 'boolean',
        'profil_public'     => 'boolean',
    ];
}
