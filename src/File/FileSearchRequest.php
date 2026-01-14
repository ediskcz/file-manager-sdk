<?php

namespace Edisk\FileManager\File;

use Sovic\Common\DataList\AbstractSearchRequest;
use Sovic\Common\Helpers\StringUtil;

class FileSearchRequest extends AbstractSearchRequest
{
    protected ?string $collectionId = null;
    protected array $excludeIds = [];
    private mixed $user = null;
    protected mixed $authUser = null;

    protected ?string $filteredQuery;

    public function getCollectionId(): ?string
    {
        return $this->collectionId;
    }

    public function setCollectionId(?string $collectionId): void
    {
        if ($collectionId && !in_array($collectionId, $this->getCollectionIds(), true)) {
            $collectionId = null;
        }

        $this->collectionId = $collectionId;
    }

    public function getExcludeIds(): array
    {
        return $this->excludeIds;
    }

    public function setExcludeIds(array $excludeIds): void
    {
        $this->excludeIds = $excludeIds;
    }

    public function getUser(): mixed
    {
        return $this->user;
    }

    public function setUser(mixed $user): void
    {
        $this->user = $user;
    }

    public function getAuthUser(): mixed
    {
        return $this->authUser;
    }

    public function setAuthUser(mixed $authUser): void
    {
        $this->authUser = $authUser;
    }

    protected function getDefaultLimit(): int
    {
        return 32;
    }

    public function getCollectionIds(): array
    {
        return ['new', 'top'];
    }

    public function setSearch(?string $search): void
    {
        parent::setSearch($search);
        $this->filteredQuery = null;
    }

    public function getFilteredQuery(array $filterTerms = [], array $stopWords = []): ?string
    {
        if (!isset($this->filteredQuery)) {
            $query = $this->filterSearchQuery($this->getSearch(), $filterTerms, $stopWords);
            $this->filteredQuery = $query;
        }

        return $this->filteredQuery;
    }

    public function getSortingTypes(): array
    {
        return ['date', 'size', 'relevance'];
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['per_page'] = $this->getLimit(); // temp, backward compatibility
        $array['p'] = $this->getPage(); // p = page, for route pagination links, 0-based to 1-based
        $array['query'] = $this->getSearch() ?? ''; // temp, backward compatibility
        $array['exclude_ids'] = $this->getExcludeIds();
        $array['user_id'] = $this->getUser()?->id;

        return $array;
    }

    public function filterSearchQuery(?string $query, array $filterTerms = [], array $stopWords = []): ?string
    {
        if ($query === null) {
            return null;
        }

        $query = StringUtil::toAscii($query);
        $query = str_replace(['_', '.', '-'], ' ', $query);
        $query = preg_replace('/\s+/', ' ', $query);
        $query = trim($query, ' -');
        $query = mb_strtolower($query);

        $parts = explode(' ', $query);
        // $filterTerms = (array) Kohana::$config->load('search.filter_terms');
        // $stopWords = (array) Kohana::$config->load('search.stop_words');

        $minLength = 3; // ft_min_word_len
        $filteredParts = [];
        foreach ($parts as $part) {
            if (in_array($parts, $filterTerms, true) || in_array($part, $stopWords, true)) {
                continue;
            }

            // extend episodes numbers
            if (((int) $part) > 0 && mb_strlen($part) <= 2 && mb_strlen($part) === mb_strlen((int) $part)) {
                $filteredParts[] = 'epi' . $part;
                continue;
            }

            // extend unfiltered short words
            if (mb_strlen($part) < $minLength) {
                $filteredParts[] = 'exp' . $part;
                continue;
            }

            $filteredParts[] = $part;
        }

        return implode(' ', $filteredParts);
    }
}
