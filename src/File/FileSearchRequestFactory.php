<?php

namespace Edisk\FileManager\File;

use Sovic\Common\DataList\Enum\LayoutId;
use Sovic\Common\Helpers\StringUtil;

class FileSearchRequestFactory
{
    public const int DefaultLimit = 32;
    public const int MaxQueryLength = 100;

    public static function loadFromParameters(array $parameters): FileSearchRequest
    {
        $request = new FileSearchRequest();

        // pagination
        $limit = (int) ($parameters['limit'] ?? self::DefaultLimit);
        $request->setLimit($limit);
        $page = (int) ($parameters['page'] ?? 1);
        $request->setPage($page);

        // sort
        $sort = $parameters['sort'] ?? null;
        $request->setSort($sort);

        // layout
        $layout = $parameters['layout'] ?? 'grid';
        $layoutId = LayoutId::tryFrom($layout);
        $request->setLayoutId($layoutId);

        // collection
        $collection = $parameters['collection'] ?? null;
        $request->setCollectionId($collection);

        // search query
        $search = $parameters['search'] ?? '';
        $search = strip_tags(stripslashes($search));
        $search = substr($search, 0, self::MaxQueryLength);
        if (!empty($search)) {
            $search = str_replace(['_', '.', '-'], ' ', $search); // update to querystring and remove
            $search = StringUtil::fixUtf8($search);
            $request->setSearch($search);
        }

        return $request;
    }
}
