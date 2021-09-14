<?php require_once __DIR__ . '/includes/_header.php';?>

<div class="p-3">
    <h2 class="mb-4">
        List of Uploaded Files
    </h2>

    <table class="my-4 table table-borderless table-hover datatable" data-jsonpath="files-json" data-type="<?=$type?>" data-role="<?=$_GET['role']?>">
        <thead class="thead-black">
            <tr>
                <th scope="col">#</th>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
                <th scope="col" class="pl-2" data-orderable="true" data-searchable="true" style="max-width:50%">Filename</th>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
            </tr>
        </thead>
    </table>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>