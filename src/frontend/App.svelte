<script>
    function loadStartScreen() {
        loadPaging(0, settings.capacity);
        loadPage(0, settings.capacity);
    }

    let sample = '';

    async function search(sample) {
        window.$('#paging').empty();
        window.$.ajax({
            type: "GET",
            url: `/api/v1/task/search/${sample}/`,
            dataType: "json",
            success: renderTasks,
            error: function (jqXHR, textStatus, errorThrown) {
            },
            timeout: 500,
        });
    }

    async function applySample(event) {

        event.preventDefault();
        if (sample) {
            search(sample);
        }
        if (!sample) {
            loadStartScreen();
        }
    }

</script>

<svelte:window on:load = {loadStartScreen} />

<div class="container">
    <h1>Трекер рабочих заданий</h1>

    <form class="form-horizontal" id="search"
    on:submit="{applySample}">
        <div class="form-group">
            <label class="control-label col-sm-2" for="sample">
            Поиск
            </label>
            <div class="col-sm-10">
                <input id="sample" class="form-control " type="search"
                 placeholder="Введите наименование задачи"
                 autofocus autocomplete="on"
                 bind:value={sample}
                 />
            </div>
        </div>
    </form>
    <div id="taskList">
    </div>
    <div id="paging">
    </div>

  <div class="modal fade" id="detailTaskView" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header" style="padding:35px 50px;">
                <h4><span class="glyphicon glyphicon-list"></span> Информация по задаче #<span id="id"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
        <div class="modal-body">
          <form role="form">
            <div class="form-group">
              <label for="title"><span class="glyphicon glyphicon-text-color"></span> Заголовок</label>
              <input type="text" class="form-control" id="title" placeholder="Значение не задано" disabled>
            </div>
            <div class="form-group">
              <label for="date"><span class="glyphicon glyphicon-time"></span> Дата выполнения</label>
              <input type="text" class="form-control" id="date" placeholder="Значение не задано" disabled>
            </div>
            <div class="form-group">
              <label for="author"><span class="glyphicon glyphicon-user"></span> Автор</label>
              <input type="text" class="form-control" id="author" placeholder="Значение не задано" disabled>
            </div>
            <div class="form-group">
              <label for="status"><span class="glyphicon glyphicon-check"></span> Статус</label>
              <input type="text" class="form-control" id="status" placeholder="Значение не задано" disabled>
            </div>
            <div class="form-group">
              <label for="description"><span class="glyphicon glyphicon-pencil"></span> Описание</label>
              <textarea class="form-control" id="description" placeholder="Значение не задано" disabled></textarea>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

</div>
