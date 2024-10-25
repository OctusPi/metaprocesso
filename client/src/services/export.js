import { nextTick } from 'vue';
import html2pdf from 'html2pdf.js';
import utils from '@/utils/utils';

async function exportPDF(data, exportname) {
    utils.load(true)
    try {
        const optionExport = {
            margin: 10,
            filename: exportname+'.pdf',
            image: { type: 'jpeg', quality:0.98},
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait'}
        }
        
        await nextTick()
        html2pdf().from(data).set(optionExport).save()
    } catch (error) {
        console.log(error)
    } finally {
        utils.load(false)
    }
    
}

async function generatePDF(data) {
    utils.load(true)
    try {
        const optionExport = {
            margin: 10,
            filename: 'nulldoc.pdf',
            image: { type: 'jpeg', quality:0.98},
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait'}
        }

        await nextTick()
        return html2pdf().set(optionExport).from(data).toPdf().output('blob')
    } catch (error) {
        throw console.error(error)
    } finally {
        utils.load(false)
    }
}

export default {
    exportPDF,
    generatePDF
}