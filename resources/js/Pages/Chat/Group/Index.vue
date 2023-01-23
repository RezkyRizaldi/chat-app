<script setup>
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import { Head, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import GroupChatBubble from "@/Components/Chat/Group/Bubble.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { PaperAirplaneIcon } from "@heroicons/vue/24/solid";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const messages = ref([]);

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

onMounted(() => {
	Echo.channel("group-chat").listen("GroupChatEvent", ({ groupMessage }) => {
		messages.value.push(groupMessage);
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
							<GroupChatBubble :messages="messages" v-else />
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
