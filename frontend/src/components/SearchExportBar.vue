<template>
  <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
    
    <!-- Search Input -->
    <div class="relative w-full md:w-96">
      <input 
        v-model="searchQuery" 
        type="text" 
        placeholder="Search products..." 
        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
      />
      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>

    <!-- Sort & Export Buttons -->
    <div class="flex items-center gap-3 w-full md:w-auto">
      <!-- Sorting Toggle -->
      <button 
        @click="toggleSort" 
        class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700"
      >
        <span>{{ sortOrder === 'asc' ? 'A - Z' : 'Z - A' }}</span>
        <svg v-if="sortOrder === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path></svg>
      </button>

      <!-- Export Dropdown -->
      <div class="relative group">
        <button class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-bold shadow-sm">
          <span>Export Report</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        
        <!-- Dropdown Menu -->
        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">
          <button @click="exportData('excel')" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium border-b border-gray-50">📊 Export to Excel</button>
          <button @click="exportData('pdf')" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium border-b border-gray-50">📄 Export to PDF</button>
          <button @click="exportData('html')" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium border-b border-gray-50">🌐 Export to HTML</button>
          <button @click="exportData('print')" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium">🖨️ Print Report</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import * as XLSX from 'xlsx-js-style'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

const props = defineProps<{
  data: any[]
  atbiName: string
  atbiFullName: string
  atbiSlug: string
}>()

const emit = defineEmits(['update'])

const searchQuery = ref('')
const sortOrder = ref<'asc' | 'desc'>('asc')

watch([searchQuery, sortOrder], () => {
  filterAndSort()
})

function toggleSort() {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
}

function filterAndSort() {
  let result = [...props.data]
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(item => 
      item.name.toLowerCase().includes(query) || 
      (item.description && item.description.toLowerCase().includes(query))
    )
  }
  
  result.sort((a, b) => {
    const nameA = a.name.toLowerCase()
    const nameB = b.name.toLowerCase()
    if (sortOrder.value === 'asc') return nameA.localeCompare(nameB)
    return nameB.localeCompare(nameA)
  })
  
  emit('update', result)
}

// Get the correct school logo path based on slug
function getSchoolLogoPath() {
  const slug = props.atbiSlug?.toLowerCase() || ''
  if (slug.includes('1') || slug.includes('bsu')) return '/logos/bsu.png'
  if (slug.includes('2') || slug.includes('clsu')) return '/logos/clsu.png'
  if (slug.includes('3') || slug.includes('dmmmsu')) return '/logos/dmmmsu.png'
  if (slug.includes('4') || slug.includes('isu')) return '/logos/isu.png'
  if (slug.includes('5') || slug.includes('mmsu')) return '/logos/mmsu.png'
  if (slug.includes('6') || slug.includes('psau') || slug.includes('psu')) return '/logos/psu.png'
  return '/logos/main.png'
}

// --- HELPER: Convert Image URL to Circular Base64 (Fixes PDF square logos) ---
async function getCircularLogoBase64(imageUrl: string): Promise<string> {
  return new Promise((resolve) => {
    const img = new Image()
    img.crossOrigin = 'Anonymous' // Needed for CORS
    img.src = imageUrl
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const size = Math.min(img.width, img.height)
      canvas.width = size
      canvas.height = size
      const ctx = canvas.getContext('2d')
      
      if (ctx) {
        // Draw circle mask
        ctx.beginPath()
        ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2)
        ctx.clip()
        // Draw image inside mask
        ctx.drawImage(img, 0, 0, size, size)
        resolve(canvas.toDataURL('image/png'))
      } else {
        resolve('')
      }
    }
    img.onerror = () => resolve('')
  })
}

// --- HTML GENERATOR (For HTML, Print, and PDF styling) ---
function generateReportHTML(mainLogoB64: string = '', schoolLogoB64: string = '') {
  const date = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  
  const leftLogo = mainLogoB64 || `${window.location.origin}/logos/main.png`
  const rightLogo = schoolLogoB64 || `${window.location.origin}${getSchoolLogoPath()}`

  const rows = props.data.map((p, i) => `
    <tr style="border-bottom: 1px solid #e5e7eb;">
      <td style="padding: 10px; text-align: center;">${i + 1}</td>
      <td style="padding: 10px; font-weight: bold; color: #111827;">${p.name}</td>
      <td style="padding: 10px; color: #4b5563;">${p.description || 'N/A'}</td>
      <td style="padding: 10px; text-align: right; font-weight: bold; color: #047857;">₱${Number(p.price).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
    </tr>
  `).join('')

  return `
    <!DOCTYPE html>
    <html style="height: 100%;">
    <head>
      <meta charset="UTF-8">
      <title>${props.atbiName} Report</title>
      <style>
        /* CSS FOR SCREEN VIEW & PRINT */
        body { 
          font-family: Arial, sans-serif; 
          background: white; 
          margin: 0; 
          padding: 20px; 
          height: 100%; 
          box-sizing: border-box;
          display: flex;
          flex-direction: column;
        }
        
        .report-container { 
          width: 100%; 
          max-width: 1000px; 
          margin: 0 auto; 
          flex: 1; /* This makes the container stretch to fill the page */
          display: flex;
          flex-direction: column;
        }
        
        .content-wrap {
          flex: 1; /* This pushes the footer to the bottom */
        }
        
        /* HEADER & FOOTER STYLES */
        .header { display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to right, #10b981, #3b82f6); padding: 20px; border-radius: 8px 8px 0 0; color: white; }
        .footer { margin-top: 30px; text-align: center; color: white; font-size: 12px; background: #10b981; padding: 15px; border-radius: 0 0 8px 8px; }
        
        /* TABLE STYLES */
        table { width: 100%; border-collapse: collapse; font-size: 14px; border: 1px solid #e5e7eb; border-top: none; }
        th { background-color: #10b981; color: white; padding: 12px; text-align: left; }
        th.center { text-align: center; width: 50px; }
        th.price { text-align: right; width: 120px; }

        /* CSS FOR PRINT VIEW (Fixes layout and colors) */
        @media print {
          @page { margin: 15mm; }
          body { padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; height: 100vh; }
          
          /* Force background colors to print */
          .header { background-color: #10b981 !important; color: white !important; }
          .footer { background-color: #10b981 !important; color: white !important; }
          table th { background-color: #10b981 !important; color: white !important; }
          
          /* Prevent rows from splitting across pages */
          tr { page-break-inside: avoid; }
        }
      </style>
    </head>
    <body>
      <div class="report-container">
        
        <!-- HEADER -->
        <div class="header">
          <img src="${leftLogo}" style="width: 80px; height: 80px; object-fit: contain; background: white; border-radius: 50%; padding: 4px;" />
          <div style="text-align: center;">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 1px;">ATBI Intertrade System</h1>
            <h2 style="margin: 5px 0 0 0; font-size: 16px; font-weight: normal; color: #ecfdf5;">${props.atbiFullName}</h2>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #eff6ff;">Generated on: ${date}</p>
          </div>
          <img src="${rightLogo}" style="width: 80px; height: 80px; object-fit: contain; background: white; border-radius: 50%; padding: 4px;" />
        </div>

        <!-- CONTENT WRAPPER (This stretches and pushes the footer down) -->
        <div class="content-wrap">
          <!-- TABLE -->
          <table>
            <thead>
              <tr>
                <th class="center">#</th>
                <th>Product Name</th>
                <th>Description</th>
                <th class="price">Price</th>
              </tr>
            </thead>
            <tbody>
              ${rows || '<tr><td colspan="4" style="text-align:center; padding: 20px;">No products found.</td></tr>'}
            </tbody>
          </table>
        </div>

        <!-- FOOTER -->
        <div class="footer">
          <p style="margin: 0; font-weight: bold;">ATBI Intertrade System</p>
          <p style="margin: 4px 0 0 0; color: #ecfdf5;">&copy; ${new Date().getFullYear()} All rights reserved.</p>
          <p style="margin: 4px 0 0 0; color: #eff6ff;">Agribased Technology Business Incubator</p>
        </div>

      </div>
    </body>
    </html>
  `
}

// --- MASTER EXPORT FUNCTION ---
async function exportData(type: 'excel' | 'pdf' | 'html' | 'print') {
  
  // 1. PRINT
  if (type === 'print') {
    const printWindow = window.open('', '_blank')
    if (printWindow) {
      printWindow.document.write(generateReportHTML())
      printWindow.document.close()
      
      // Wait for images to load before triggering print
      printWindow.onload = () => {
        setTimeout(() => {
          printWindow.focus()
          printWindow.print()
        }, 500)
      }
    }
    return
  }

  // 2. HTML
  if (type === 'html') {
    const blob = new Blob([generateReportHTML()], { type: 'text/html' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.atbiName}_Report.html`
    a.click()
    URL.revokeObjectURL(url)
    return
  }

  // 3. EXCEL (Styled using xlsx-js-style)
  if (type === 'excel') {
    const wsData = [
      // Header Row 1
      [
        { v: 'ATBI Intertrade System', s: { font: { bold: true, size: 16, color: { rgb: "10b981" } } } }, 
        { v: '' }, 
        { v: '' }
      ],
      // Header Row 2
      [
        { v: props.atbiFullName, s: { font: { bold: true, size: 12, color: { rgb: "3b82f6" } } } }, 
        { v: '' }, 
        { v: '' }
      ],
      // Header Row 3
      [
        { v: `Generated: ${new Date().toLocaleDateString()}`, s: { font: { italic: true, size: 10, color: { rgb: "666666" } } } }, 
        { v: '' }, 
        { v: '' }
      ],
      [], // Empty spacer row
      // Table Headers
      [
        { v: 'Product Name', s: { font: { bold: true, color: { rgb: "FFFFFF" } }, fill: { fgColor: { rgb: "10b981" } }, border: { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } } } },
        { v: 'Description', s: { font: { bold: true, color: { rgb: "FFFFFF" } }, fill: { fgColor: { rgb: "10b981" } }, border: { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } } } },
        { v: 'Price', s: { font: { bold: true, color: { rgb: "FFFFFF" } }, fill: { fgColor: { rgb: "10b981" } }, border: { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } } } }
      ]
    ]
    
    props.data.forEach(p => {
      wsData.push([
        { v: p.name, s: { font: { bold: true }, border: { bottom: { style: "thin", color: { rgb: "dddddd" } } } } },
        { v: p.description || 'N/A', s: { border: { bottom: { style: "thin", color: { rgb: "dddddd" } } } } },
        { v: Number(p.price), s: { numFmt: '"₱"#,##0.00', font: { bold: true, color: { rgb: "047857" } }, border: { bottom: { style: "thin", color: { rgb: "dddddd" } } } } }
      ])
    })

    const ws = XLSX.utils.aoa_to_sheet(wsData)
    ws['!cols'] = [{ wch: 35 }, { wch: 50 }, { wch: 15 }]
    ws['!merges'] = [
      { s: { r: 0, c: 0 }, e: { r: 0, c: 2 } }, // Merge System Name
      { s: { r: 1, c: 0 }, e: { r: 1, c: 2 } }, // Merge ATBI Name
      { s: { r: 2, c: 0 }, e: { r: 2, c: 2 } }  // Merge Date
    ]

    const wb = XLSX.utils.book_new()
    
    // Add Print Header & Footer for Excel
    wb.Props = {
      Title: `${props.atbiName} Products Report`,
      Subject: "ATBI Intertrade System",
      Author: "ATBI",
      CreatedDate: new Date()
    }
    ws['!pageSetup'] = {
      orientation: 'landscape',
      fitToWidth: 1,
      fitToHeight: 0,
      header: "&C&\"Arial,Bold\"&14ATBI Intertrade System&\"Arial,Regular\"&12\n${props.atbiFullName}",
      footer: "&C&\"Arial,Regular\"&10© 2026 ATBI Intertrade System. All rights reserved."
    }

    XLSX.utils.book_append_sheet(wb, ws, 'Products')
    XLSX.writeFile(wb, `${props.atbiName}_Report.xlsx`)
    return
  }

  // 4. PDF
  if (type === 'pdf') {
    const btn = document.querySelector('button')
    if (btn) btn.innerText = 'Generating...'

    // Get circular base64 logos
    const mainLogoB64 = await getCircularLogoBase64('/logos/main.png')
    const schoolLogoB64 = await getCircularLogoBase64(getSchoolLogoPath())

    const doc = new jsPDF()
    
    // Lighter header background
    doc.setFillColor(16, 185, 129) // #10b981 (Emerald 500)
    doc.rect(0, 0, 210, 35, 'F')
    
    // Add circular logos
    if (mainLogoB64) doc.addImage(mainLogoB64, 'PNG', 15, 3, 28, 28)
    if (schoolLogoB64) doc.addImage(schoolLogoB64, 'PNG', 167, 3, 28, 28)

    // Header Text
    doc.setFontSize(18)
    doc.setTextColor(255, 255, 255)
    doc.text('ATBI Intertrade System', 105, 15, { align: 'center' })
    
    doc.setFontSize(12)
    doc.setTextColor(236, 253, 245) // Very light green
    doc.text(props.atbiFullName, 105, 23, { align: 'center' })
    
    doc.setFontSize(9)
    doc.setTextColor(239, 246, 255) // Very light blue
    doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 105, 29, { align: 'center' })

    // Table Data
    const tableColumn = ["#", "Product Name", "Description", "Price"]
    const tableRows: any[] = []

    props.data.forEach((p, i) => {
      tableRows.push([
        i + 1, 
        p.name, 
        p.description || 'N/A', 
        // Use PHP symbol and formatted numbers
        `PHP ${Number(p.price).toLocaleString('en-US', { minimumFractionDigits: 2 })}`
      ])
    })

    autoTable(doc, {
      head: [tableColumn],
      body: tableRows,
      startY: 40,
      theme: 'grid',
      headStyles: { fillColor: [16, 185, 129], textColor: [255, 255, 255], fontStyle: 'bold' }, // Lighter green
      styles: { fontSize: 9, cellPadding: 4 },
      columnStyles: {
        0: { halign: 'center', cellWidth: 15 },
        // Right-align the price column properly
        3: { halign: 'right', cellWidth: 35, textColor: [4, 120, 87], fontStyle: 'bold' }
      },
      alternateRowStyles: { fillColor: [245, 250, 248] }
    })

    // Footer Background
    const pageHeight = doc.internal.pageSize.height
    doc.setFillColor(16, 185, 129)
    doc.rect(0, pageHeight - 15, 210, 15, 'F')

    // Footer Text
    doc.setFontSize(8)
    doc.setTextColor(255, 255, 255)
    doc.text(
      `© ${new Date().getFullYear()} ATBI Intertrade System. All rights reserved.`,
      105, 
      pageHeight - 6, 
      { align: 'center' }
    )

    doc.save(`${props.atbiName}_Report.pdf`)
    
    if (btn) btn.innerText = 'Export Report'
  }
}
</script>