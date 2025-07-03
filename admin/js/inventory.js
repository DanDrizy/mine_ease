
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function openEditModal(item, table) {
            document.getElementById('edit_id').value = item.id;
            document.getElementById('in_id').value = item.in_id;
            document.getElementById('edit_table').value = table;
            document.getElementById('edit_item_name').value = item.item_name;
            document.getElementById('edit_category').value = item.category;
            document.getElementById('edit_quantity').value = item.quantity;
            document.getElementById('edit_quantity_out').value = item.quantity_out;
            document.getElementById('edit_unit_price').value = item.unit_price;
            
            openModal('editModal');
        }

        function openDeleteModal(id, table, itemName) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_table').value = table;
            document.getElementById('delete_item_name').textContent = itemName;
            
            openModal('deleteModal');
        }

        function redirectToAddPage(type) {
            if (type === 'stockin') {
                window.location.href = 'add_stockin.php';
            } else {
                window.location.href = 'add_stockout.php';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target === modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }
    