<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    team: Object,
});

const form = useForm({
    colored_logo: null,
    bw_logo: null,
});

const coloredLogoPreview = ref(props.team.colored_logo ? `${props.team.colored_logo}` : null);
const bwLogoPreview = ref(props.team.bw_logo ? `${props.team.bw_logo}` : null);

const updateTeamLogos = () => {
    form.post(route('team-logos.update', props.team), {
        errorBag: 'updateTeamLogos',
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const updateColoredLogoPreview = (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            coloredLogoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
        form.colored_logo = file;
    }
};

const updateBWLogoPreview = (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            bwLogoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
        form.bw_logo = file;
    }
};
</script>

<template>
    <FormSection @submitted="updateTeamLogos">
        <template #title>
            Team Logos
        </template>

        <template #description>
            Update your team's colored and black & white logos.
            Will be used for the QR code logo.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="colored_logo" value="Colored Logo" />
                <input
                    type="file"
                    id="colored_logo"
                    class="block mt-1 w-full"
                    @change="updateColoredLogoPreview"
                    accept="image/*"
                />
                <InputError :message="form.errors.colored_logo" class="mt-2" />
                <div v-if="coloredLogoPreview" class="mt-2">
                    <img :src="coloredLogoPreview" alt="Colored Logo Preview" class="max-h-32">
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="bw_logo" value="Black & White Logo" />
                <input
                    type="file"
                    id="bw_logo"
                    class="block mt-1 w-full"
                    @change="updateBWLogoPreview"
                    accept="image/*"
                />
                <InputError :message="form.errors.bw_logo" class="mt-2" />
                <div v-if="bwLogoPreview" class="mt-2">
                    <img :src="bwLogoPreview" alt="Black & White Logo Preview" class="max-h-32">
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
