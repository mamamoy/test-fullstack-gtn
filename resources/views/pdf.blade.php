<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>

    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td valign="top"><img src="https://ugc.production.linktr.ee/3ea7255d-bdee-43cf-bdc4-44db798c9cb3_Customre-Care---Admin.jpeg?io=true&size=avatar-v3_0" /></td>
            <td align="right">
                <h3>PT. Griya Telko Nusantara</h3>
                <pre>
                Company representative name
                Company address
                Tax ID
                phone
                fax
            </pre>
            </td>
        </tr>

    </table>

    <table width="100%">
        <tr>
            <td><strong>Exported By:</strong> {{ $user->name }}</td>
            <td><strong>Time Downloaded:</strong> {{ date('H:i:s d/m/Y', strtotime(now())) }}</td>

        </tr>

    </table>

    <br />

    <table width="100%">
        <thead style="background-color: lightgray;">
            <tr>
                <th>#</th>
                <th>Sales Name</th>
                <th>Total Selling</th>
                <th>Approved</th>
                <th>Total Rp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userData as $index => $report)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $report->name }}</td>
                    <td align="right">{{ count($report->sales_reports) }}</td>
                    <td align="right">{{ count($report->sales_reports->where('isApproved', 1)) }}</td>
                    <td align="right">{{ $earningUsers[$report->id] }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td align="right">Subtotal Rp</td>
                <td align="right">{{ $subtotal }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td align="right">Tax Rp</td>
                <td align="right">{{ $subtotal * 0.11 }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td align="right">Total Rp</td>
                <td align="right" class="gray">{{ $subtotal - $subtotal * 0.11 }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
