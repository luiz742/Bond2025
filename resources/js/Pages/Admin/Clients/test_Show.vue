// resources/js/Pages/Admin/Clients/test_Show.vue
<script setup>
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import Show from './Show.vue'

// Mock child components
vi.mock('@/Layouts/AdminLayout.vue', () => ({ default: { template: '<div><slot /><slot name="header" /></div>' } }))
vi.mock('@/Components/InputLabel.vue', () => ({ default: { template: '<label><slot /></label>' } }))
vi.mock('@/Components/InputError.vue', () => ({ default: { template: '<div><slot /></div>' } }))
vi.mock('@/Components/PrimaryButton.vue', () => ({ default: { template: '<button><slot /></button>' } }))

// Mock Inertia useForm
const postMock = vi.fn()
const putMock = vi.fn()
vi.mock('@inertiajs/vue3', () => ({
    useForm: () => ({
        files: {},
        errors: {},
        processing: false,
        post: postMock,
        put: putMock,
        client_id: 1,
        status: '',
        id: null,
        rejection_reason: '',
    }),
}))

// Mock route helper
global.route = vi.fn((name, id) => `/mocked/${name}/${id || ''}`)

const baseProps = {
    user: { id: 1, name: 'Admin' },
    client: {
        id: 1,
        name: 'Test Client',
        service: {
            country: 'Vanuatu',
            documents: [
                { id: 1, name: 'Passport', client_type: 'main', type: 'personal' },
                { id: 2, name: 'Marriage Cert', client_type: 'spouse', type: 'personal' },
                { id: 3, name: 'Company Cert', client_type: 'main', type: 'company' },
            ],
        },
        files: [
            { id: 10, document_id: 1, status: 'pending', path: 'passport.pdf' },
            { id: 11, document_id: 2, status: 'rejected', path: 'marriage.pdf', rejection_reason: 'Blurry' },
            { id: 12, document_id: 3, status: 'approved', path: 'company.pdf' },
        ],
    },
    familyMembers: [{ label: 'SPOUSE' }],
}

describe('Show.vue', () => {
    let wrapper

    beforeEach(() => {
        postMock.mockClear()
        putMock.mockClear()
        wrapper = mount(Show, { props: baseProps })
    })

    it('renders tabs for client and family members', () => {
        expect(wrapper.text()).toContain('Client')
        expect(wrapper.text()).toContain('Spouse')
    })

    it('filters client documents by tab and search', async () => {
        expect(wrapper.html()).toContain('Passport')
        await wrapper.find('input[type="text"]').setValue('pass')
        expect(wrapper.html()).toContain('Passport')
        await wrapper.find('input[type="text"]').setValue('xyz')
        expect(wrapper.html()).not.toContain('Passport')
    })

    it('shows correct status and actions for each document', () => {
        // Pending: should show Approve/Reject
        expect(wrapper.html()).toContain('Status:')
        expect(wrapper.html()).toContain('Approve')
        expect(wrapper.html()).toContain('Reject')
        // Rejected: should show rejection reason and reupload input
        expect(wrapper.html()).toContain('Rejection Reason:')
        expect(wrapper.html()).toContain('Blurry')
        // Approved: should show View File
        expect(wrapper.html()).toContain('View File')
    })

    it('submits file upload for a document', async () => {
        // Find file input for a document without file (simulate new doc)
        const newDoc = { id: 99, name: 'New Doc', client_type: 'main', type: 'personal' }
        wrapper.setProps({
            ...baseProps,
            client: {
                ...baseProps.client,
                service: {
                    ...baseProps.client.service,
                    documents: [...baseProps.client.service.documents, newDoc],
                },
                files: baseProps.client.files,
            },
        })
        await flushPromises()
        const fileInput = wrapper.find('input[type="file"]:not([disabled])')
        const file = new File(['dummy'], 'dummy.pdf', { type: 'application/pdf' })
        await fileInput.trigger('change', { target: { files: [file] } })
        // Simulate submit
        const submitBtn = wrapper.findAllComponents({ name: 'PrimaryButton' }).find(btn => btn.text() === 'Submit')
        await submitBtn.trigger('click')
        expect(postMock).toHaveBeenCalled()
    })

    it('approves a pending document', async () => {
        const approveBtn = wrapper.findAllComponents({ name: 'PrimaryButton' }).find(btn => btn.text() === 'Approve')
        await approveBtn.trigger('click')
        expect(postMock).toHaveBeenCalled()
    })

    it('opens and submits rejection modal', async () => {
        const rejectBtn = wrapper.findAllComponents({ name: 'PrimaryButton' }).find(btn => btn.text() === 'Reject')
        await rejectBtn.trigger('click')
        await flushPromises()
        expect(wrapper.html()).toContain('Reason for Rejection')
        // Fill reason and submit
        const textarea = wrapper.find('textarea')
        await textarea.setValue('Not clear')
        const submitRejBtn = wrapper.findAllComponents({ name: 'PrimaryButton' }).find(btn => btn.text().toLowerCase().includes('submit rejection'))
        await submitRejBtn.trigger('click')
        expect(postMock).toHaveBeenCalled()
    })

    it('handles reupload for rejected document', async () => {
        // Find reupload input for rejected doc
        const reuploadInput = wrapper.findAll('input[type="file"]').find(input => input.attributes('name') === 'file')
        const file = new File(['dummy'], 'dummy.pdf', { type: 'application/pdf' })
        await reuploadInput.trigger('change', { target: { files: [file] } })
        // Find and click reupload button
        const reuploadBtn = wrapper.findAllComponents({ name: 'PrimaryButton' }).find(btn => btn.text().toLowerCase().includes('reenviar'))
        await reuploadBtn.trigger('click')
        expect(putMock).toHaveBeenCalled()
    })
})
</script>