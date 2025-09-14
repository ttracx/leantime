<?php

namespace Safe4Work\Domain\Plugins\Models;

use Illuminate\Support\Str;
use Safe4Work\Domain\Plugins\Contracts\PluginDisplayStrategy;

class InstalledPlugin implements PluginDisplayStrategy
{
    public ?int $id;

    public string $name;

    public bool $enabled;

    public string $description;

    public string $version;

    public string $imageUrl = '';

    public string $vendorDisplayName;

    public int $vendorId;

    public string $vendorEmail;

    public string $installdate;

    public string $foldername;

    public string $homepage;

    public string|array $authors;

    public ?string $format;

    public ?string $license;

    public ?string $type;

    public ?bool $installed;

    public ?string $startingPrice;

    public ?string $calculatedMonthlyPrice;

    public ?string $identifier;

    public function getCardDesc(): string
    {
        return $this->description ??= '';
    }

    /**
     * Retrieves the metadata links for the plugin.
     *
     * The metadata links include author's email, author's name, plugin version, and homepage URL.
     * If the authors are not empty, the email of the first author is included as a link.
     * If the version is not empty, the plugin version is included as a link.
     * If the homepage is not empty, the homepage URL is included as a link.
     *
     * @return array An array of metadata links.
     */
    public function getMetadataLinks(): array
    {
        $links = [];

        if (! empty($this->vendorDisplayName) && (! empty($this->vendorId) || ! empty($this->vendorEmail))) {
            $vendor = [
                'prefix' => __('text.by'),
                'display' => $this->vendorDisplayName,
            ];

            $vendor['link'] = ! empty($this->vendorId) ? '/plugins/marketplace?'.http_build_query(['vendor_id' => $this->vendorId]) : "mailto:{$this->vendorEmail}";

            $links[] = $vendor;
        }

        if (! empty($this->authors) && (is_array($this->authors) || is_object($this->authors))) {
            $author = is_array($this->authors) ? $this->authors[0] : $this->authors;

            if (is_object($author)) {
                $links[] = [
                    'prefix' => __('text.by'),
                    'link' => "mailto:{$author->email}",
                    'text' => $author->name,
                ];
            }
        }

        if (! empty($this->version)) {
            $links[] = [
                'prefix' => __('text.version'),
                'text' => $this->version,
            ];
        }

        if (! empty($this->homepage)) {
            $links[] = [
                'link' => $this->homepage,
                'text' => __('text.visit_site'),
            ];
        }

        return $links;
    }

    public function getControlsView(): string
    {
        return 'plugins::partials.installed.plugincontrols';
    }

    public function getPluginImageData(): string
    {
        if (! empty($this->imageUrl) && $this->imageUrl != 'false') {
            return $this->imageUrl;
        }

        if (file_exists($image = APP_ROOT.'/app/Plugins/'.str_replace('.', '', $this->foldername).'/screenshot.png')) {
            // Read image path, convert to base64 encoding
            $imageData = base64_encode(file_get_contents($image));

            return 'data: '.mime_content_type($image).';base64,'.$imageData;
        }

        $image = APP_ROOT.'/public/dist/images/svg/undraw_search_app_oso2.svg';
        $imageData = base64_encode(file_get_contents($image));

        return 'data: '.mime_content_type($image).';base64,'.$imageData;
    }

    public function getPrice(): string
    {
        if (! empty($this->startingPrice)) {
            return __('text.starting_at').' '.$this->startingPrice;
        }

        return '';
    }

    public function getCalulatedMonthlyPrice(): string
    {
        if (! empty($this->calculatedMonthlyPrice)) {
            return $this->calculatedMonthlyPrice;
        }

        return '';
    }

    public function getType(): string
    {
        $this->type = $this->format === 'phar'
            ? $this->type = 'marketplace'
            : $this->type = 'custom';

        return $this->type;
    }

    public function getIdentifier(): string
    {
        if (isset($this->identifier) && $this->identifier !== null && $this->identifier !== '') {
            return $this->identifier;
        }

        // There are circumstances where name needs to be used. The root cause has been fixed and identifier should
        // be set most of the time however in rare circumstances we need to make sure the name is built correctly
        $name = Str::replace('/', '_', Str::lower($this->name));
        if (Str::contains($name, '_') === false) {
            $name = 'leantime_'.$name;
        }

        return $name;
    }
}
