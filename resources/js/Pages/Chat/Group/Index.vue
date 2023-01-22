<script setup>
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import moment from "moment";
import { computed, onMounted, onUpdated, ref } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { PaperAirplaneIcon } from "@heroicons/vue/24/solid";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const messages = ref([]);
const bubble = ref(null);

const user = computed(() => usePage().props.auth.user);

const form = useForm({ message: "" });

const fetchMessages = async () => {
	const { data } = await axios.get(route("group-conversations.index"));

	messages.value = data;
};

const sendMessage = () => {
	form.post(route("group-conversations.store"), {
		onSuccess: () => {
			form.reset();
			fetchMessages();
		},
		preserveScroll: true,
	});
};

onUpdated(() => {
	bubble.value.scrollTop = bubble.value.lastElementChild.offsetTop;
});

onMounted(() => {
	Echo.channel("group-chat").listen("GroupChatEvent", ({ groupMessage }) => {
		console.log("Listen");
		messages.value.push({ message: groupMessage.message });
	});
	fetchMessages();
});
</script>

<template>
	<Head title="Group Chat" />

	<AuthenticatedLayout>
		<div class="py-12">
			<div class="mx-auto max-w-xs sm:max-w-7xl sm:px-6 lg:px-8">
				<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
					<div class="h-[31rem] p-6 text-gray-900 sm:h-[28rem]">
						<div
							class="flex h-full flex-col items-center justify-between gap-y-6"
						>
							<div
								v-if="!messages.length"
								class="flex flex-1 flex-col items-center justify-center gap-y-2.5 py-3"
							>
								<ChatBubbleLeftRightIcon
									class="h-10 w-10 text-gray-400 sm:h-20 sm:w-20"
								/>
								<span class="text-center text-sm italic text-gray-400"
									>Mulai mengobrol dengan orang-orang.</span
								>
							</div>
							<div
								class="flex w-full flex-1 flex-col gap-y-4 overflow-y-scroll py-3 pr-6 scrollbar-thin scrollbar-track-gray-400 scrollbar-thumb-black scrollbar-track-rounded-full scrollbar-thumb-rounded-full"
								ref="bubble"
								v-else
							>
								<div v-for="message in messages" :key="message.id">
									<div class="flex flex-col gap-y-1.5">
										<span
											:class="`text-sm${
												user.id === message.user.id ? ' self-end' : ''
											}`"
											>{{ message.user.name }}</span
										>
										<div
											:class="`flex items-end gap-x-2.5${
												user.id === message.user.id
													? ' flex-row-reverse self-end'
													: ''
											}`"
										>
											<div
												:class="`w-fit space-x-2 rounded-xl p-4 ${
													user.id === message.user.id
														? 'bg-blue-400'
														: 'bg-gray-400'
												}`"
											>
												<span class="text-white">{{ message.message }}</span>
											</div>
											<span class="text-xs">{{
												moment(message.created_at).format("HH:mm")
											}}</span>
										</div>
									</div>
								</div>
							</div>
							<div class="w-full">
								<form
									class="flex items-center gap-x-4"
									@submit.prevent="sendMessage"
								>
									<div class="flex-1">
										<TextInput
											aria-label="Chat"
											type="text"
											class="block w-full"
											placeholder="Kirim pesan"
											v-model="form.message"
											required
											autofocus
										/>
									</div>

									<div>
										<PrimaryButton
											:class="{ 'opacity-25': form.processing }"
											:disabled="form.processing"
										>
											<PaperAirplaneIcon class="h-6 w-6" />
										</PrimaryButton>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
