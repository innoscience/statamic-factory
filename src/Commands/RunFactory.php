<?php

namespace Aerni\Factory\Commands;

use Aerni\Factory\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;

class RunFactory extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statamic:factory:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake content with the factory';

    /**
     * The factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Factory $factory)
    {
        parent::__construct();

        $this->factory = $factory;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->hasContent()) {
            $content = $this->choice('Choose the type of content you want to create', $this->content());
        }

        if ($content === 'Collection') {
            $handle = $this->choice('Choose a collection', $this->collections());
            $amount = $this->askValid(
                "How many ${handle} do you want to create?",
                'amount',
                ['required', 'numeric', 'min:1']
            );
        }

        if ($content === 'Taxonomy') {
            $handle = $this->choice('Choose a taxonomy', $this->taxonomies());
            $amount = $this->askValid(
                "How many ${handle} do you want to create?",
                'amount',
                ['required', 'numeric', 'min:1']
            );
        }

        $this->runFactory($content, $handle, $amount);
    }

    /**
     * Run the factory.
     *
     * @param string $content
     * @param string $handle
     * @param string $amount
     * @return void
     */
    protected function runFactory(string $content, string $handle, string $amount): void
    {
        try {
            $this->factory->run($content, $handle, $amount);
            $this->info('Your fake content was successfully created!');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Get an array of available content types.
     *
     * @return array
     */
    protected function content(): array
    {
        $content = [];

        if ($this->hasCollections()) {
            $content[] = 'Collection';
        }
        if ($this->hasTaxonomies()) {
            $content[] = 'Taxonomy';
        }

        return $content;
    }

    /**
     * Get an array of available collection handles.
     *
     * @return array
     */
    protected function collections(): array
    {
        return Collection::handles()->toArray();
    }

    /**
     * Get an array of available taxonomy handles.
     *
     * @return array
     */
    protected function taxonomies(): array
    {
        return Taxonomy::handles()->toArray();
    }

    /**
     * Check if there's any content.
     *
     * @return bool
     */
    protected function hasContent(): bool
    {
        if (empty($this->content())) {
            $this->error('You need at least one collection or taxonomy to use the factory.');

            return false;
        }

        return true;
    }

    /**
     * Check if there's any collections.
     *
     * @return bool
     */
    protected function hasCollections(): bool
    {
        if (empty($this->collections())) {
            return false;
        }

        return true;
    }

    /**
     * Check if there's any taxonomies.
     *
     * @return bool
     */
    protected function hasTaxonomies(): bool
    {
        if (empty($this->taxonomies())) {
            return false;
        }

        return true;
    }

    /**
     * Validate the answer of a question.
     *
     * @param string $question
     * @param string $field
     * @param array $rules
     * @return string
     */
    protected function askValid(string $question, string $field, array $rules): string
    {
        $value = $this->ask($question);

        if ($message = $this->validateInput($rules, $field, $value)) {
            $this->error($message);

            return $this->askValid($question, $field, $rules);
        }

        return $value;
    }

    /**
     * Validate the input.
     *
     * @param array $rules
     * @param string $fieldName
     * @param string $value
     * @return mixed
     */
    protected function validateInput(array $rules, string $fieldName, string $value)
    {
        $validator = Validator::make([
            $fieldName => $value,
        ], [
            $fieldName => $rules,
        ]);

        return $validator->fails()
            ? $validator->errors()->first($fieldName)
            : null;
    }
}
