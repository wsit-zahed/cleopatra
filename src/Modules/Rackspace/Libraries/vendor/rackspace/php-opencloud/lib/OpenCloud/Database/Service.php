<?php
/**
 * Copyright 2012-2014 Rackspace US, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenCloud\Database;

use OpenCloud\Common\Service\NovaService;
use OpenCloud\Database\Resource\Instance;

/**
 * The Rackspace Database service
 */
class Service extends NovaService
{
    const DEFAULT_TYPE = 'rax:database';
    const DEFAULT_NAME = 'cloudDatabases';

    /**
     * Returns an Instance
     *
     * @param string $id the ID of the instance to retrieve
     * @return \OpenCloud\Database\Resource\Instance
     */
    public function instance($id = null)
    {
        return $this->resource('Instance', $id);
    }

    /**
     * Returns a Collection of Instance objects
     *
     * @param array $params
     * @return \OpenCloud\Common\Collection\PaginatedIterator
     */
    public function instanceList($params = array())
    {
        $url = clone $this->getUrl();
        $url->addPath(Instance::resourceName())->setQuery($params);

        return $this->resourceList('Instance', $url);
    }
}
