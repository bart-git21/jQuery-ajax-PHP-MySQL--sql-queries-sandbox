<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выполнение запроса</title>

    <!-- bootstrap & jQuery -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</head>

<body>
    <header><?php include "../layouts/header.php" ?></header>
    <main class="container">
        <h1 class="text-center">Query sandbox</h1>

        <div class="card mb-3" style="width: 18rem;">
            <div class="card-body">
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="queriesSelect">
                        <option selected disabled value="-1">Выбор запроса</option>
                    </select>
                </div>
                <div class="border mb-2" id="queryText">
                    <div>Текст запроса (только чтение)</div>
                </div>

                <div>
                    <!-- Button trigger modal -->
                    <button class="btn-sm" type="button" id="modalBtn" data-toggle="modal" data-target="#myModal">
                        Редактирование
                    </button>
                    <!-- Button trigger table controller -->
                    <button class="btn-sm" id="requestQueryBtn">Выполнить</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Результат запроса
            </div>
            <div class="card-body">
                <div id="table"></div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактирование</h5>
                    <button type="button" class="close btn-sm" id="closeModalBtn" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="border mb-2">
                        <input id="modalQueryName" type="text" title="Название запроса">
                    </div>
                    <div class="border mb-2">
                        <textarea id="modalQueryText" type="text" title="Текст запроса"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="editModalBtn" type="button" class="btn btn-primary btn-sm">Редактировать</button>
                    <button id="createModalBtn" type="button" class="btn btn-primary btn-sm">Создать</button>
                </div>
            </div>
        </div>
    </div>

    <script defer type="module">
        import { TableModel, TableView, TableController } from "./../components/table.js";
        import { selectModel, selectView, selectController } from "./../components/select.js";

        $(document).ready(function () {
            (function () {
                const user = localStorage.getItem("user");
                if (!user) {
                    $("#homeBtn").click();
                    location.href = "/client/index.php";
                }
            })();

            let queriesSelect = {};
            $.ajax({
                url: "/api/query/",
                method: "GET",
            })
                .done(response => {
                    queriesSelect = new selectController(new selectView("#queriesSelect"), new selectModel(response));
                    queriesSelect.create();
                    queriesSelect.startChangeListener();
                })
                .fail()
                .always()
            $("#requestQueryBtn").on("click", function () {
                const createTable = new TableController(new TableView(), new TableModel(queriesSelect.store.queryResult));
                createTable.display();
            })

            // Bootstrap modal usage
            $('#myModal').on('shown.bs.modal', function (e) {
                const selectedOption = $('#queriesSelect').find(`option[value="${queriesSelect.store.id}"]`);
                $("#modalQueryName").val(selectedOption.text());
                $("#modalQueryText").val(queriesSelect.store.queryText);
            })
            $('#myModal').on('hidden.bs.modal', function (e) {
                var modal = $(this);
                modal.find('#modalQueryName').val("");
                modal.find('#modalQueryText').val("");
            })
            $("#editModalBtn").on("click", function () {
                const id = +queriesSelect.store.id;
                const editedQuery = {
                    id,
                    name: $("#modalQueryName").val(),
                    query: $("#modalQueryText").val(),
                    userId: localStorage.getItem('userId')
                };
                $.ajax({
                    url: `/api/query/?id=${id}`,
                    method: "PUT",
                    data: JSON.stringify(editedQuery),
                    headers: { "Content-Type": "application/json" },
                })
                    .done(response => {
                        console.log(response.success);
                        queriesSelect.update(editedQuery);
                        $("#queriesSelect").find(`option[value="${queriesSelect.store.id}"]`).text(editedQuery.name);
                        $("#queryText").text(editedQuery.query);
                        $("#queriesSelect").trigger('change');
                        $('#myModal').modal('hide');
                    })
                    .fail((xhr, status, err) => { console.error("Error: ", err) })
                    .always()
            })
            $("#createModalBtn").on("click", function () {
                const newQuery = {
                    name: $("#modalQueryName").val(),
                    query: $("#modalQueryText").val(),
                    userID: localStorage.getItem('userId')
                };
                $.ajax({
                    url: "/api/query/",
                    method: "POST",
                    data: JSON.stringify(newQuery),
                    headers: { "Content-Type": "application/json" },
                })
                    .done(response => {
                        queriesSelect.update({
                            id: +response.newQueryId,
                            name: newQuery.name,
                            query: newQuery.query,
                            userID: localStorage.getItem('userId')
                        });
                        $(`#selectId option[value='${response.newQueryId}']`).prop("selected", true);
                        $('#myModal').modal('hide');
                    })
                    .fail((xhr, status, err) => { console.error("Error: ", err) })
                    .always()
            })
        })
    </script>
</body>

</html>
