<?php
    function RenderFilters($conn){
        $sql = 'SELECT category_name FROM categories';

        $result = mysqli_query($conn, $sql);

        $count = 1;
        while($row = mysqli_fetch_row($result)) {
            echo '<div class="checkbox-item">
                    <input type="checkbox" class="category-filter"id="cat' . $count . '" name="categories[]" value="' . $row[0] . '">
                    <label for="cat' . $count . '">' . $row[0] . '</label>
                    </div>';
            $count++;
        }
    }
?>