var cacheName = 'Pinkvilla-pwa'; 
var filesToCache = [
'',    
'../webpage1.php',  
'../webpage2.html',  
'pinterest_grid.js'];  

self.addEventListener('install', function(e) { 
e.waitUntil(
caches.open(cacheName).then(function(cache) { 
return cache.addAll(filesToCache);   
})    
);  
}); 
/* Serve cached content when offline */ 
self.addEventListener('fetch', function(e) {  
e.respondWith( caches.match(e.request).then(function(response) {  
return response || fetch(e.request);
})   
);  
});