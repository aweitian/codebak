<?PHP
$descriptorspec = array(
   0 => array("pipe", "r"),  // ��׼���룬�ӽ��̴Ӵ˹ܵ��ж�ȡ����
   1 => array("pipe", "w"),  // ��׼������ӽ�����˹ܵ���д������
   STDERR // ��׼����д�뵽һ���ļ�
);

$cwd = '/tmp';
$env = array('some_option' => 'aeiou');

$process = proc_open('php', $descriptorspec, $pipes, 'c:\\xampp\php', [
	'PATH' => 'C:\\Tcl\\bin;C:\\Program Files (x86)\\Java\\jdk1.8.0_111\\bin;%SystemRoot%\\system32;%SystemRoot%;%SystemRoot%\\System32\\Wbem;%SYSTEMROOT%\\System32\\WindowsPowerShell\\v1.0\\;D:\\adt-bundle-windows-x86-20140702\\sdk\\platform-tools;C:\\Program Files\\nodejs\\;C:\\Program Files (x86)\\Bitvise SSH Client;D:\\HashiCorp\\Vagrant\\bin;C:\\ProgramData\\ComposerSetup\\bin;C:\\Program Files (x86)\\Lua\\5.1;C:\\Program Files (x86)\\Lua\\5.1\\clibs'
]);

if (is_resource($process)) {
    // $pipes ���ڿ������������ģ�
    // 0 => �������ӽ��̱�׼����д��ľ��
    // 1 => ���Դ��ӽ��̱�׼�����ȡ�ľ��
    // �����������׷�ӵ��ļ� /tmp/error-output.txt

    fwrite($pipes[0], '<?php print_r($_ENV); ?>');
    fclose($pipes[0]);

    echo stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    

    // �мǣ��ڵ��� proc_close ֮ǰ�ر����еĹܵ��Ա���������
    $return_value = proc_close($process);

    echo "command returned $return_value";
}
