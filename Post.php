<?php


namespace App\Models;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post
{

    public  $title;

    public $excerpt;

    public $date;

    public $body;

    public $slug;
    /**
     * Post constructor.
     * Constructor dla metadanych z posta + cały post.
     * @param $title
     * @param $excerpt
     * @param $date
     * @param $body
     */
    public function __construct($title, $excerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function all()
    {
        // kolekcja laravel jak widoki na sterydach
        return cache()->rememberForever('posts.all', function (){
            return collect(File::files(resource_path("posts")))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                ))
                ->sortByDesc('date');
        });

    }

    public static function find($slug)
    {
        // ze wszystkich postów znajduje nam konkretny slug który jest podany w requescie
       return static::all()->firstWhere('slug', $slug);

    }

    public static function findOrFail($slug)
    {
        // ze wszystkich postów znajduje nam konkretny slug który jest podany w requescie
        $post = static::find($slug);

        // jak nie ma takiego posta to rzuci 404
        if (! $post) {
            throw new ModelNotFoundException();
        }

        return $post;
    }
}
