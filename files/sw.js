var CACHE_NAME = 'admin-<?=$cacheKey?>';
var adminPrefix = '<?=$this->model->_Admin->getUrlPrefix()?>';
var urlsToCache = <?=json_encode($assets)?>;

self.addEventListener('install', function(event) {
	event.waitUntil(
		caches.open(CACHE_NAME).then(function(cache) {
			console.log('Caching the App Shell...');
			return fetch(adminPrefix, {
				credentials: 'include'
			}).then(function(response) {
				if (!response.ok) {
					throw new TypeError('Bad response status');
				}
				return cache.put(adminPrefix, response);
			}).then(function(){
				console.log('Caching the assets...');
				return cache.addAll(urlsToCache);
			})
		}).then(function(){
			console.log('Resources added to the cache');
		}).catch(function(err){
			console.log(err);
		})
	);
});

self.addEventListener('activate', function(event) {
	event.waitUntil(
		caches.keys().then(function(cacheNames) {
			return Promise.all(
				cacheNames.map(function(cacheName) {
					if (cacheName !== CACHE_NAME) {
						console.log('Clearing old cache ' + cacheName);
						return caches.delete(cacheName);
					}
				})
			);
		})
	);
});

self.addEventListener('fetch', function(event) {
	event.respondWith(
		caches.match(event.request).then(function(response) {
			// Cache hit - return response
			if (response) {
				console.log('Found!');
				return response;
			}

			return fetch(event.request);
		})
	);
});
