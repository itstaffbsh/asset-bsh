<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWTR - {{ $assetRequest->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #f0f0f0;
        }

        .page-wrapper {
            max-width: 794px;
            margin: 20px auto;
            background: white;
            padding: 20px 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        }

        /* ── Print bar ── */
        .print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c5282;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 12px;
        }
        .print-bar a { color: #bee3f8; text-decoration: none; }
        .btn-print {
            background: #a47b53; color: white; border: none;
            padding: 8px 18px; border-radius: 5px;
            cursor: pointer; font-size: 13px; font-weight: bold;
        }

        /* ── Section header (biru) ── */
        .section-header {
            background: #4472c4;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            padding: 4px 6px;
            letter-spacing: 0.5px;
        }

        /* ── TOP HEADER area ── */
        .top-area {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }
        .logo-title { display: flex; align-items: center; gap: 12px; }
        .logo-img { height: 40px; }
        .doc-title {
            margin-left: 30px;
            font-size: 8pt;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }

        /* ── Doc info box ── */
        .doc-info-box {
            font-size: 7.5pt;
            min-width: 200px;
        }
        .doc-info-box table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .doc-info-box td { padding: 2px 5px; border: 1px solid #000; }
        .doc-info-box td:first-child { font-weight: bold; width: 90px; }

        /* ── Master Table ── */
        .master-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 15px;
        }
        .master-table th, .master-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            vertical-align: middle;
        }
        .master-header {
            background: #2b78e4; /* Matching the blue from the image */
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            padding: 4px 6px;
            letter-spacing: 0.5px;
        }
        .col-1 { width: 1%; white-space: nowrap; padding-right: 2px; }
        .col-2 { text-align: center; }
        .col-3 { width: 1%; white-space: nowrap; text-align: center; }
        .col-4 { width: 140px; }
        .col-5 { width: 90px; text-align: center; }
        .col-6 { width: 90px; text-align: center; }
        .col-7 { width: auto; }
        
        .label-cell { background: #f5f5f5; }

        /* ── Checkbox styling ── */
        .checkbox-group { display: flex; align-items: center; gap: 8px; }
        .cb { display: inline-flex; align-items: center; gap: 2px; font-size: 8.5pt; }
        .cb-box {
            width: 11px; height: 11px;
            border: 1px solid #000;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 9pt; font-weight: bold; line-height: 1;
        }
        .cb-box.checked { background: #000; color: #fff; }

        /* ── Signature area ── */
        .sig-section { border: 1px solid #000; }
        .sig-row { display: flex; }
        .sig-box { flex: 1; border-right: 1px solid #000; text-align: center; display: flex; flex-direction: column; }
        .sig-box:last-child { border-right: none; }
        .sig-img-area { flex: 1; min-height: 60px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 2px 0; }
        .sig-img { max-height: 50px; max-width: 100%; object-fit: contain; mix-blend-mode: multiply; }
        .sig-name { font-size: 8.5pt; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 2px 0; }
        .sig-date { display: flex; font-size: 8.5pt; text-align: left; }
        .sig-date .date-label { border-right: 1px solid #000; padding: 2px 4px; width: 40px; }
        .sig-date .date-value { padding: 2px 4px; flex: 1; }
        .sig-font { font-family: 'Dancing Script', cursive; font-size: 20pt; color: #000; }
        .comment-box { flex: 2; padding: 4px 6px; font-size: 8.5pt; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .comment-text { margin: 0; padding: 2px 0; text-align: center; }

        /* ── IT Approval checkboxes ── */
        .it-status-row { display: flex; gap: 20px; align-items: center; margin-bottom: 6px; }

        /* ── Print media ── */
        @media print {
            @page { size: A4; margin: 0; }
            body { 
                padding: 1.5cm 1.8cm;
                background: white; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            * {
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            .page-wrapper {
                margin: 0; padding: 0;
                box-shadow: none;
                max-width: 100%;
                width: 100%;
            }
            .print-bar { display: none !important; }
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    {{-- Tombol Aksi (tidak muncul saat cetak) --}}
    <div class="print-bar">
        <a href="{{ url()->previous() }}">← Kembali</a>
        <span style="font-weight:bold;">📋 EWTR — {{ $assetRequest->user->name }}</span>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>

    {{-- ===== TOP HEADER ===== --}}
    <div class="top-area">
        {{-- Logo + Judul --}}
        <div class="logo-title">
            <img src="{{ asset('images/Primier-Logo.webp') }}" alt="Logo" class="logo-img" onerror="this.style.display='none'">
            <div class="doc-title">EMPLOYEE WORKING TOOLS REQUEST</div>
        </div>

        {{-- Doc Info Box --}}
        <div class="doc-info-box">
            <table>
                <tr><td>Docs No.</td><td>III-001/01</td></tr>
                <tr><td>Date</td><td>2-jan-25</td></tr>
                <tr><td>Revise</td><td>00</td></tr>
                <tr><td>Revise Date</td><td></td></tr>
                <tr><td>Classification</td><td><em>Internal Use</em></td></tr>
            </table>
        </div>
    </div>

    {{-- ===== MASTER TABLE FOR PERFECT ALIGNMENT ===== --}}
    @php
        $criticality = $assetRequest->criticality_level;
        $defaultTools1 = ['Personal Computer'];
        $defaultTools2 = [
            'Monitor / TV Display 43"', 'Cell Phone', 'Cell Phone Number',
            'Bali Super Host Email', 'Subscribe Envato', 'Guesty',
            'Position/Department on ERP', 'Respond.io', 'Google Drive'
        ];
        $toolsCount = count($defaultTools1) + count($assetRequest->items) + count($defaultTools2);
    @endphp

    <table class="master-table">
        {{-- 1. REQUEST NUMBER ROW --}}
        <tr>
            <td colspan="3" style="text-align: center;">Request Number</td>
            <td class="col-4" style="text-align: center;">Request Date</td>
            <td class="col-5" style="text-align: center;">{{ $assetRequest->created_at->format('d-M-Y') }}</td>
            <td class="col-6" style="text-align: center;">Requestor</td>
            <td class="col-7" style="text-align: center;"><strong>HR Department</strong></td>
        </tr>
        <tr>
            <td class="col-1">BSH/FRM/HRD/III-</td>
            <td class="col-2">{{ str_pad($assetRequest->id, 3, '0', STR_PAD_LEFT) }}</td>
            <td class="col-3">0</td>
            <td class="col-4">Criticality Level</td>
            <td class="col-5">
                <span class="cb"><span class="cb-box {{ $criticality == 'top priority' ? 'checked' : '' }}">{{ $criticality == 'top priority' ? '✓' : '' }}</span> Top Priority</span>
            </td>
            <td class="col-6">
                <span class="cb"><span class="cb-box {{ $criticality == 'urgent' ? 'checked' : '' }}">{{ $criticality == 'urgent' ? '✓' : '' }}</span> Urgent</span>
            </td>
            <td class="col-7">
                <span class="cb"><span class="cb-box {{ $criticality == 'normal' ? 'checked' : '' }}">{{ $criticality == 'normal' ? '✓' : '' }}</span> Normal</span>
            </td>
        </tr>

        {{-- 2. EMPLOYEE DETAILS --}}
        <tr>
            <td colspan="7" class="master-header">EMPLOYEE DETAILS</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Employee Name</td>
            <td colspan="4">{{ $assetRequest->user->name }}</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Job Position</td>
            <td colspan="4">{{ $assetRequest->user->job_position ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Department</td>
            <td colspan="4">{{ $assetRequest->department->nama_departemen }}</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Email</td>
            <td colspan="4" style="color: #2b78e4;">{{ $assetRequest->user->email }}</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Phone Number</td>
            <td colspan="4" style="text-decoration: underline;">{{ $assetRequest->user->phone_number ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Location / Area</td>
            <td colspan="4">
                @php $office = \App\Models\Office::find($assetRequest->user->office_id); @endphp
                {{ $office ? $office->nama_kantor : '-' }}
            </td>
        </tr>

        {{-- 3. EMPLOYEE WORKING TOOLS --}}
        <tr>
            <td colspan="7" class="master-header">EMPLOYEE WORKING TOOLS</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell" style="text-align:center; vertical-align:middle; background:white;" rowspan="{{ $toolsCount + 1 }}">Employee Working Tools</td>
            <td class="col-4" style="background: #e2e8f0; text-align: center; font-weight: bold; padding: 1px;">Category</td>
            <td colspan="2" style="background: #e2e8f0; text-align: center; font-weight: bold; padding: 1px;">Status</td>
            <td class="col-7" style="background: #e2e8f0; text-align: center; font-weight: bold; padding: 1px;">Remarks</td>
        </tr>

        @php $rowIndex = 0; @endphp
        
        {{-- Default Tools 1 --}}
        @foreach($defaultTools1 as $tool)
        <tr>
            <td class="col-4">{{ $tool }}</td>
            <td class="col-5" style="text-align:left; padding-left:6px;"><span class="cb"><span class="cb-box"></span> YES</span></td>
            <td class="col-6" style="text-align:left; padding-left:6px;"><span class="cb"><span class="cb-box checked">✓</span> NO</span></td>
            <td class="col-7"></td>
        </tr>
        @php $rowIndex++; @endphp
        @endforeach

        {{-- Requested Items --}}
        @foreach($assetRequest->items as $i => $item)
        <tr>
            <td class="col-4"><strong>{{ $item->classification->nama_klasifikasi }}</strong></td>
            <td class="col-5" style="text-align:left; padding-left:6px;"><span class="cb"><span class="cb-box checked">✓</span> YES</span></td>
            <td class="col-6" style="text-align:left; padding-left:6px;"><span class="cb"><span class="cb-box"></span> NO</span></td>
            <td class="col-7">
                {{ $item->specs ?? '' }}
                @if($assetRequest->admin_notes && $i === 0)
                    <em>{{ $assetRequest->admin_notes }}</em>
                @endif
            </td>
        </tr>
        @php $rowIndex++; @endphp
        @endforeach

        {{-- Default Tools 2 --}}
        @foreach($defaultTools2 as $tool)
        <tr>
            <td class="col-4">{{ $tool }}</td>
            <td class="col-5" style="text-align:left; padding-left:6px;"><span class="cb"><span class="cb-box"></span> YES</span></td>
            <td class="col-6" style="text-align:left; padding-left:6px;"><span class="cb"><span class="cb-box checked">✓</span> NO</span></td>
            <td class="col-7"></td>
        </tr>
        @php $rowIndex++; @endphp
        @endforeach

        {{-- User Roles Justification --}}
        <tr>
            <td colspan="3" class="label-cell" style="background:white; border-left:1px solid #000;">User Roles Justification</td>
            <td colspan="4" style="padding:4px 6px;">
                <div class="checkbox-group">
                    <span class="cb"><span class="cb-box"></span> Super Admin</span>
                    <span class="cb" style="margin-left:55px;"><span class="cb-box"></span> Admin</span>
                    <span class="cb" style="margin-left:35px;"><span class="cb-box checked">✓</span> User</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== APPROVAL OF THE REQUISITION SECTION ===== --}}
    @php
        $deptApproval = $assetRequest->approvals->where('level', 'dept')->first();
        $hrApproval   = $assetRequest->approvals->where('level', 'hr')->first();
    @endphp
    <div class="section-header">APPROVAL OF THE REQUISITION SECTION</div>
    <div class="sig-section">
        <div class="sig-row" style="border-bottom: 1px solid #000;">
            <div style="flex: 0 0 25%; width: 25%; border-right:1px solid #000; text-align: center; padding:3px 8px; font-weight:bold; font-size:8.5pt;">Requested By</div>
            <div style="flex: 0 0 25%; width: 25%; border-right:1px solid #000; text-align: center; padding:3px 8px; font-weight:bold; font-size:8.5pt;">HOD / Manager</div>
            <div style="flex: 0 0 50%; width: 50%; text-align: center; padding:3px 8px; font-weight:bold; font-size:8.5pt;">Review &amp; Comments</div>
        </div>
        <div class="sig-row">
            {{-- HR (Requested By) --}}
            <div class="sig-box" style="flex: 0 0 25%; width: 25%;">
                <div class="sig-img-area">
                    @if($assetRequest->hrManager)
                        @if($assetRequest->hrManager->signature_path)
                            <img src="{{ asset('storage/' . $assetRequest->hrManager->signature_path) }}" class="sig-img" alt="TTD">
                        @else
                            <span class="sig-font">{{ $assetRequest->hrManager->name }}</span>
                        @endif
                    @endif
                </div>
                <div class="sig-name">{{ $assetRequest->hrManager?->name ?? '________________________' }}</div>
                <div class="sig-date">
                    <div class="date-label">Date</div>
                    <div class="date-value">{{ $hrApproval?->created_at?->format('d M Y') ?? $assetRequest->created_at->format('d M Y') }}</div>
                </div>
            </div>
            {{-- HOD/Manager Dept --}}
            <div class="sig-box" style="flex: 0 0 25%; width: 25%;">
                <div class="sig-img-area">
                    @if($deptApproval?->user)
                        @if($deptApproval->user->signature_path)
                            <img src="{{ asset('storage/' . $deptApproval->user->signature_path) }}" class="sig-img" alt="TTD">
                        @else
                            <span class="sig-font">{{ $deptApproval->user->name }}</span>
                        @endif
                    @endif
                </div>
                <div class="sig-name">{{ $deptApproval?->user?->name ?? '________________________' }}</div>
                <div class="sig-date">
                    <div class="date-label">Date</div>
                    <div class="date-value">{{ $deptApproval?->created_at?->format('d M Y') ?? '' }}</div>
                </div>
            </div>
            {{-- Review & Comments (HR + Dept) --}}
            <div class="comment-box" style="flex: 0 0 50%; width: 50%;">
                @if($assetRequest->hr_comment || $assetRequest->dept_comment)
                    @if($assetRequest->hr_comment)
                        <p class="comment-text"><strong>HR:</strong> {{ $assetRequest->hr_comment }}</p>
                    @endif
                    @if($assetRequest->dept_comment)
                        <p class="comment-text" style="margin-top:4px;"><strong>Dept:</strong> {{ $assetRequest->dept_comment }}</p>
                    @endif
                @endif
                @if($assetRequest->user->join_date)
                    <p style="font-size:8pt; margin-top:6px; text-align:center;"><strong>Join Date: {{ \Carbon\Carbon::parse($assetRequest->user->join_date)->format('d F Y') }}</strong></p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== IT APPROVAL SECTION ===== --}}
    @php
        $itApproval = $assetRequest->approvals->where('level', 'it')->first();
        $itApproved = $itApproval !== null;
    @endphp
    <div class="section-header">IT APPROVAL SECTION</div>
    <div class="sig-section" style="margin-top:0;">
        <div style="padding:4px 8px; border-bottom:1px solid #000;">
            <div class="it-status-row">
                <span style="font-weight:bold; font-size:10pt;">IT Approval</span>
                <span class="cb">
                    <span class="cb-box checked" style="font-size: 10pt; margin-left:80px;">{{ $itApproved ? '✓' : '' }}</span> Approve
                </span>
                <span class="cb">
                    <span class="cb-box" style="font-size: 10pt; margin-left:80px;"></span> Pending
                </span>
                <span class="cb">
                    <span class="cb-box" style="font-size: 10pt; margin-left:80px;"></span> Rejected
                </span>
            </div>
        </div>
        <div class="sig-row">
            <div class="sig-box" style="flex: 0 0 25%; width: 25%;">
                <div style="font-weight:bold; padding:3px 0; border-bottom: 1px solid #000; font-size: 8.5pt;">{{ $itApproval?->user?->job_position ?? 'IT Manager' }}</div>
                <div class="sig-img-area">
                    @if($itApproval?->user)
                        @if($itApproval->user->signature_path)
                            <img src="{{ asset('storage/' . $itApproval->user->signature_path) }}" class="sig-img" alt="TTD">
                        @else
                            <span class="sig-font">{{ $itApproval->user->name }}</span>
                        @endif
                    @endif
                </div>
                <div class="sig-name">{{ $itApproval?->user?->name ?? '________________________' }}</div>
                <div class="sig-date">
                    <div class="date-label">Date</div>
                    <div class="date-value">{{ $itApproval?->created_at?->format('d M Y') ?? '' }}</div>
                </div>
            </div>
            <div class="comment-box" style="flex: 0 0 75%; width: 75%; align-items: stretch;">
                <div style="font-weight:bold; text-align:center; padding-bottom: 4px; font-size: 8.5pt;">Review &amp; Comments</div>
                <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                    @if($assetRequest->it_comment)
                        <p class="comment-text">{{ $assetRequest->it_comment }}</p>
                    @endif
                    @if($assetRequest->admin_notes)
                        <p class="comment-text" style="margin-top:4px;">{{ $assetRequest->admin_notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MANAGEMENT APPROVAL ===== --}}
    @php
        $mdApproval = $assetRequest->approvals->where('level', 'md')->first();
    @endphp
    <div class="section-header">Management Approval</div>
    <div class="sig-section" style="margin-top:0;">
        <div class="sig-row">
            <div class="sig-box" style="flex: 0 0 25%; width: 25%;">
                <div style="font-weight:bold; padding:3px 0; border-bottom: 1px solid #000; font-size: 8.5pt;">Managing Director</div>
                <div class="sig-img-area">
                    @if($mdApproval?->user)
                        @if($mdApproval->user->signature_path)
                            <img src="{{ asset('storage/' . $mdApproval->user->signature_path) }}" class="sig-img" alt="TTD">
                        @else
                            <span class="sig-font">{{ $mdApproval->user->name }}</span>
                        @endif
                    @endif
                </div>
                <div class="sig-name">{{ $mdApproval?->user?->name ?? '________________________' }}</div>
                <div class="sig-date">
                    <div class="date-label">Date</div>
                    <div class="date-value">{{ $mdApproval?->created_at?->format('d M Y') ?? '' }}</div>
                </div>
            </div>
            <div class="comment-box" style="flex: 0 0 75%; width: 75%; align-items: stretch;">
                <div style="font-weight:bold; text-align:center; padding-bottom: 4px; font-size: 8.5pt;">Review &amp; Comments</div>
                <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                    @if($assetRequest->md_comment)
                        <p class="comment-text">{{ $assetRequest->md_comment }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
