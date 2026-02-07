<template>

<v-container>
    <v-card class="py-6">
        <v-card-title class="text-h6">Notes</v-card-title>
        <v-card-text>
            <!-- Search bar -->
             <v-text-field
                v-model="q"
                label="Search judul"
                variant="outlined"
                density="comfortable"
                clearable
             />
            <!-- Formulir -->
             <v-form @submit.prevent="submit">
                <div class="d-flex ga-2 align-center">
                    <v-text-field
                        v-model="form.title"
                        label="Judul Note"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.title ? [form.errors.title] : []"
                    />
                    <v-btn type="submit" :loading="form.processing" color="primary">
                        Tambah
                    </v-btn>
                </div>
             </v-form>

                <!-- List -->
                 <v-list lines="one">
                    <v-list-item v-for="n in notes.data" :key="n.id">
                        <template #title>
                            <div v-if="editingId===n.id" class="d-flex ga-2 align-center">
                                <v-text-field
                                    v-model="editForm.title"
                                    label="Edit judul"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                    :error-messages="editForm.errors.title ? [editForm.errors.title] : []"
                                />
                                <v-btn
                                    color="primary"
                                    :loading="editForm.processing"
                                    @click="saveEdit(n.id)"
                                >
                                    Simpan
                                </v-btn>
                                <v-btn variant="tonal" @click="cancelEdit">
                                    Batal
                                </v-btn>
                            </div>
                            <div v-else class="d-flex justify-space-between align-center">
                                <div>{{n.title}}</div>
                                <div class="d-flex ga-2">
                                    <v-btn size="small" variant="tonal" @click="startEdit(n)">
                                        Edit
                                    </v-btn>
                                    <v-btn size="small" variant="tonal" color="error" @click="remove(n.id)">
                                        Hapus
                                    </v-btn>


                                </div>

                            </div>
                        </template>

                    </v-list-item>

                 </v-list>
                     <!-- bagian pagination -->
     <Pagination :links="notes.links"/>


             



        </v-card-text>
    </v-card>
</v-container>
</template>

<script setup>
import {useForm, router} from '@inertiajs/vue3'
import {ref, watch} from 'vue';
import Pagination from '@/Components/Pagination.vue';

//untuk mendefinisikan bentuk data
const props = defineProps({
    //karena pakai paginator dan filter
    notes: Object, //untuk paginator
    filters: Object, // untuk query

    //untuk yang polos tanpa pagination
    // notes: Array
})

//variabel untuk simpan query
const q = ref(props.filters?.q ?? '')

let t = null
watch(q, (val) => {
    clearTimeout(t)
    t = setTimeout(() => {
        router.get(
            route('notes.index'),
            {q: val || ''},
            {preserveState: true, replace: true}
        )
    }, 400) //debounce 400ms
})

//untuk mendefinisikan form default
const form = useForm({
    title: '',
})

//konstanta untuk mendefinisikan id form yang di edit
const editingId = ref(null)

//konstanta untuk mendefinisikan form editing
const editForm = useForm({
    title: '',
})


//fungsi submit untuk memasukkan data baru
function submit(){
    form.post(route('notes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('title'),
    })
}

//fungsi untuk menghapus data
function remove(id){
    if(!confirm('Yakin hapus note ini?')) return
    form.delete(route('notes.destroy', id), {
        preserveScroll: true,
    })
}

//fungsi untuk mengedit data
function startEdit(note){
    editingId.value = note.id
    editForm.title = note.title
    editForm.clearErrors()
}

//fungsi untuk batal edit
function cancelEdit(){
    editingId.value = null
    editForm.reset()
}

//fungsi untuk menyimpan edit
function saveEdit(id){
    //console.log('saveEdit called', id, editForm.title)
    editForm.put(route('notes.update', id), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
        //onError: (e) => console.log('error', e)
    })
}

// //untuk pagination
// function go(url){
//     if (!url) return
//         router.visit(url, {preserveState: true})
// }
</script>