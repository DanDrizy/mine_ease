
        let searchTimeout;
        const searchInput = document.getElementById('item_search');
        const searchResults = document.getElementById('search_results');
        const selectedItemInfo = document.getElementById('selected_item_info');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                searchItems(query);
            }, 300);
        });

        function searchItems(query) {
            fetch(`?search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }

        function displaySearchResults(items) {
            if (items.length === 0) {
                searchResults.innerHTML = '<div class="search-item">No items found</div>';
                searchResults.style.display = 'block';
                return;
            }

            let html = '';
            items.forEach(item => {
                html += `
                    <div class="search-item" onclick="selectItem('${item.item_name}', '${item.category}', ${item.quantity}, ${item.unit_price}, '${item.location}', '${item.id}')">
                        <div class="item-info">
                            <div class="item-details">
                                <div class="item-name">${item.item_name}</div>
                                <div class="item-meta">${item.category} • ${item.location}</div>
                            </div>
                            <div class="item-stock">${item.quantity} available</div>
                        </div>
                    </div>
                `;
            });

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }

        function selectItem(name, category, quantity, price, location,id) {
            // Fill hidden inputs
            document.getElementById('selected_item_name').value = name;
            document.getElementById('selected_location').value = location;
            document.getElementById('id').value = id;
            
            // Update search input
            searchInput.value = name;
            
            // Fill unit price
            document.getElementById('unit_price').value = price;
            
            // Set max quantity
            document.getElementById('quantity_out').max = quantity;
            
            // Update info display
            document.getElementById('info_id').textContent = id;
            document.getElementById('info_name').textContent = name;
            document.getElementById('info_category').textContent = category;
            document.getElementById('info_quantity').textContent = quantity;
            document.getElementById('info_location').textContent = location;
            
            // Show selected item info
            selectedItemInfo.style.display = 'block';
            
            // Hide search results
            searchResults.style.display = 'none';
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        // Validate quantity input
        document.getElementById('quantity_out').addEventListener('input', function() {
            const max = parseInt(this.max);
            const value = parseInt(this.value);
            
            if (value > max) {
                this.value = max;
                alert(`Maximum available quantity is ${max}`);
            }
        });
    