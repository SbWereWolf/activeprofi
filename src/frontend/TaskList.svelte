<script>
    export let taskCollection = [];

    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();
    function show(id) {
        dispatch("show", {id:id});
    }
</script>

{#if taskCollection.length}
    <table class="table table-hover table-bordered">
    <thead>
        <tr>
            <th>Номер задачи</th>
            <th>Заголовок</th>
            <th>Дата выполнения</th>
        </tr>
    </thead>
    <tbody>
    {#each taskCollection as {id,title,date} (id)}
        <tr on:dblclick="{show(id)}">
            <td>{id}</td>
            <td>{title}</td>
            <td>{date}</td>
        </tr>
    {/each}
    </tbody>

    </table>
{/if}
