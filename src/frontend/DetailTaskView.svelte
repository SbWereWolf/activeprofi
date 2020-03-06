<script>
    export let id = 0;
    export let title = "";
    export let date = "";
    export let author = "";
    export let status = "";
    export let description = "";

    export let isModal = false;

    function disableModal() {
        isModal = false;
    }
</script>
<style>
    .task-detail-info {
        position: fixed;
        top: 5%;
        left: 5%;
    }

    .header-padding {
        padding: 35px 50px;
    }
</style>
<div class="task-detail-info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-padding">
                <h4>
                    <span class="glyphicon glyphicon-list"></span>
                    Информация по задаче # {id}</h4>
                <button type="button" class="close"
                        data-dismiss="modal"
                        on:click="{disableModal}">&times;
                </button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="form-group">
                        <label for="title">
                            <span class="glyphicon
                            glyphicon-text-color"></span>
                            Заголовок</label>
                        <input id="title" type="text"
                               class="form-control"
                               value="{title}"
                               placeholder="Значение не задано"
                               disabled>
                    </div>
                    <div class="form-group">
                        <label for="date">
                            <span class="glyphicon glyphicon-time">

                            </span>
                            Дата выполнения</label>
                        <input id="date" type="text"
                               class="form-control"
                               value="{date}"
                               placeholder="Значение не задано"
                               disabled>
                    </div>
                    <div class="form-group">
                        <label for="author"><span
                                class="glyphicon glyphicon-user">
                        </span> Автор</label>
                        <input id="author" type="text"
                               class="form-control"
                               value="{author}"
                               placeholder="Значение не задано"
                               disabled>
                    </div>
                    <div class="form-group">
                        <label for="status">
                            <span class="glyphicon glyphicon-check">
                            </span>
                            Статус</label>
                        <input id="status" type="text"
                               class="form-control"
                               value="{status}"
                               placeholder="Значение не задано"
                               disabled>
                    </div>
                    <div class="form-group">
                        <label for="description"><span
                                class="glyphicon glyphicon-pencil">
                        </span>
                            Описание</label>
                        <textarea id="description"
                                  class="form-control"
                                  bind:value="{description}"
                                  placeholder="Значение не задано"
                                  disabled></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
