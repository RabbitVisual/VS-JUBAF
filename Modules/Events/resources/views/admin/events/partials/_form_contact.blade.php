{{--
    Partial: _form_contact.blade.php
    Seção "Contato e Publicação"
    $ev = $event ?? null
--}}
@php $ev = $event ?? null; @endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="address-card" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Status, Visibilidade e Contato</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configurações de publicação e responsável pelo
                evento</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Status --}}
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status <span class="text-red-500">*</span>
            </label>
            <select name="status" id="status" required
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('status') border-red-300 @enderror">
                <option value="draft" {{ old('status', $ev?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                    Rascunho</option>
                <option value="published"
                    {{ old('status', $ev?->status ?? 'draft') === 'published' ? 'selected' : '' }}>Publicado</option>
                <option value="closed" {{ old('status', $ev?->status ?? 'draft') === 'closed' ? 'selected' : '' }}>
                    Encerrado</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Visibility --}}
        <div>
            <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Visibilidade <span class="text-red-500">*</span>
            </label>
            <select name="visibility" id="visibility" required
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('visibility') border-red-300 @enderror">
                <option value="public"
                    {{ old('visibility', $ev?->visibility ?? 'public') === 'public' ? 'selected' : '' }}>Público
                </option>
                <option value="members"
                    {{ old('visibility', $ev?->visibility ?? 'public') === 'members' ? 'selected' : '' }}>Apenas Membros
                </option>
                <option value="both"
                    {{ old('visibility', $ev?->visibility ?? 'public') === 'both' ? 'selected' : '' }}>Público +
                    Membros</option>
            </select>
            @error('visibility')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Checkboxes: featured + diretoria --}}
        <div class="md:col-span-2 flex flex-wrap gap-6">
            <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1"
                        {{ old('is_featured', $ev?->is_featured) ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-gray-600 text-yellow-500 focus:ring-yellow-500 dark:bg-gray-700">
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                        <x-icon name="star" style="duotone" class="w-4 h-4 text-yellow-500" /> Destaque na Home
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Evento aparece em primeiro na página
                        inicial</span>
                </div>
            </label>

            @if (class_exists(\Modules\Diretoria\App\Models\diretoriaApproval::class))
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="hidden" name="requires_diretoria_approval" value="0">
                        <input type="checkbox" name="requires_diretoria_approval" id="requires_diretoria_approval"
                            value="1"
                            {{ old('requires_diretoria_approval', $ev?->requires_diretoria_approval) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                            <x-icon name="shield-check" style="duotone" class="w-4 h-4 text-indigo-500" /> Requer
                            Aprovação do Conselho
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Evento fica em rascunho até o conselho
                            aprovar</span>
                    </div>
                </label>
            @endif
        </div>

        {{-- Treasury Campaign --}}
        @if (class_exists(\Modules\Treasury\App\Models\Campaign::class))
            <div class="md:col-span-2">
                <label for="treasury_campaign_id"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <x-icon name="coins" style="duotone" class="w-4 h-4 text-amber-500 inline mr-1" />
                    Campanha da Tesouraria
                </label>
                <select name="treasury_campaign_id" id="treasury_campaign_id"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                    <option value="">— Nenhuma (não vincular receita) —</option>
                    @foreach (\Modules\Treasury\App\Models\Campaign::orderBy('name')->get() as $c)
                        <option value="{{ $c->id }}"
                            {{ old('treasury_campaign_id', $ev?->treasury_campaign_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Receita das inscrições pagas será lançada nesta
                    campanha</p>
            </div>
        @endif

        {{-- Contact --}}
        <div class="md:col-span-2 pt-2">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <x-icon name="phone" style="duotone" class="w-4 h-4 text-green-500" />
                Responsável / Contato do Evento
            </h4>
        </div>

        <div>
            <label for="contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do
                Responsável</label>
            <input type="text" name="contact_name" id="contact_name"
                value="{{ old('contact_name', $ev?->contact_name) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Ex: Pr. João Silva">
        </div>

        <div>
            <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail de
                Contato</label>
            <input type="email" name="contact_email" id="contact_email"
                value="{{ old('contact_email', $ev?->contact_email) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="contato@igreja.com">
        </div>

        <div>
            <label for="contact_phone"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
            <input type="text" name="contact_phone" id="contact_phone"
                value="{{ old('contact_phone', $ev?->contact_phone) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="(11) 9999-9999">
        </div>

        <div>
            <label for="contact_whatsapp"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp</label>
            <input type="text" name="contact_whatsapp" id="contact_whatsapp"
                value="{{ old('contact_whatsapp', $ev?->contact_whatsapp) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="(11) 9999-9999">
        </div>
    </div>
</div>
