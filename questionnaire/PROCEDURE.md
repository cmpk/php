# アンケートフォーム

## 環境

### 開発環境

- Mac 10.13.6（ホストOS）
- Virtual Box 5.2.6
  - 過去にインストールしたものをそのまま利用。
- CentOS 7.8.2003（ゲストOS）
  - 2020/10/03 時点で CentOS 8 が最新だが、Virtual Box 6.1 でないとインストールできないらしい。  
    [CentOS 8 VM Doesn’t Work In VirtualBox 5.2 On Ubuntu 18.04](https://ostechnix.com/centos-8-vm-doesnt-work-in-virtualbox-5-2-on-ubuntu-18-04/)    
    実際、インストール中に画面が乱れて進めなかった...   

### 開発言語

- PHP 7.4.11
  - 2020/10/03 時点の最新
  - PDO 利用

### Webサーバ

- Apache HTTP Server 2.4.6
  - 2020/10/03 時点で Apache HTTP Server 2.4.46 が最新だが、PHP 7.4.11 は 2.4.6 に依存している模様なので、2.4.6 とする。
    - yum remove で入っていた httpd を削除しようとして発見。
- 起動ユーザは root

### データベース

- MariaDB 10.5.6
  - 2020/10/03 時点の最新。

## 環境構築手順

### 1. OS インストール

以下のサイトを参考に、ssh 可能な CentOS を作成。  

- [CentOS8 を Virtual Box にインストールする手順](https://qiita.com/yasushi-jp/items/01b4829a36272954719f)  
- [macでVirtualBoxにCentOS7をいれてssh接続する](https://qiita.com/ebkn/items/751ed657629ba8d4ab0a)  
 
**設定値**

1. VirtualBox から以下の設定で新規仮想マシンを作成。

   | 項目 | 設定値 | 備考 |
   | ---- | -------| ---- |
   | 名前   | CentOS7.8.2003_LAMP_questionnaire | 任意の値 |
   | タイプ | Linux | |
   | バージョン   | Red Hat (64-bit) | |
   | メモリサイズ | 2048 MB          | デフォルト1024MBだと遅いので増加させた |
   | ハードディスク | 仮想ハードディスクを作成する | デフォルト値 |

1. 仮想ハードディスクは以下の設定で作成。

   | 項目 | 設定値 | 備考 |
   | ---- | -------| ---- |
   | ファイルの場所 | CentOS7.8.2003_LAMP_questionnaire | デフォルト値 |
   | ファイルサイズ | 8.00 GB                       | デフォルト値 |
   | ハードディスクのファイルタイプ | VDI           | デフォルト値 |
   | 物理ファイルにあるファイルディスクのストレージ | 固定サイズ | デフォルト値（可変サイズ）だと遅かったので、高速化させたい。 |

1. VirtualBox の ファイル > ホストネットワークマネージャー から以下のアダプターを作成。  

   自分の環境には以下の設定で既に作成済みだったので、これを再利用。

   ＜アダプター＞

   | 項目 | 設定値 | 備考 |
   | ---- | -------| ---- |
   | IPv4 アドレス       | 192.168.56.1  | |
   | IPv4 ネットマスク   | 255.255.255.0 | |
   | IPv6 アドレス       | (空)          | |
   | IPv6 ネットマスク長 | 0             | |

   ＜DHCP サーバー＞

   | 項目 | 設定値 | 備考 |
   | ---- | -------| ---- |
   | サーバー アドレス | 192.168.56.100 |
   | サーバー マスク   | 255.255.255.0  |
   | アドレス下限      | 192.168.56.101 |
   | アドレス上限      | 192.168.56.254 |

1. 作成した仮想マシンを選択して 設定 > ネットワーク > アダプター2 からホストOSからゲストOSに接続できるアダプターを設定。

   | 項目 | 設定値 | 備考 |
   | ---- | -------| ---- |
   | ネットワークアダプターを有効化 | チェックをつける | |
   | 割り当て | ホストオンリーアダプター | |
   | 名前     | vboxnet 0                | 上述までの手順で作成したアダプターを選択 |

1. 続けて ストレージ から光学ドライブとしてダウンロード済みの ISO を追加。

   [http://isoredirect.centos.org/centos/7/isos/x86_64/](http://isoredirect.centos.org/centos/7/isos/x86_64/)

1. 仮想マシンを起動し、以下設定で CentOS をインストール。

   | 項目 | 設定値 | 備考 |
   | ---- | -------| ---- |
   | インストール時に使用する言語 | 日本語      | |
   | キーボード                   | 日本語      | デフォルト値 |
   | 言語サポート                 | 日本語      | デフォルト値 | 
   | 日付と時刻                   | アジア/東京 | デフォルト値 |
   | インストールソース           | 自動検出したインストールメディア | デフォルト値 |
   | ソフトウェアの選択           | 最小限のインストール | デフォルト値 |
   | インストール先 - パーティション構成 | パーティションを自動構成する | デフォルト値 |
   | KDUMP                        | 有効        | デフォルト値 |
   | ネットワークとホスト名 - ホスト名 | centos7 | |

   ネットワークは後から設定（ここで設定してもOK）。

1. root ログインして nmtui コマンドから以下を設定。

   ＜Activate a connection＞
   * 認識されている全てのイーサネットをActivate。
     * NAT と ホストオンリーアダプター で二つあるはず。

   ＜Edit a connection＞
   * enp0s3（NAT側、ホストOSと同じIPアドレス帯）
     * IPv6 を Ignore
   * enp0s8（ホストオンリーアダプター側）
     * IPv4 に 192.168.56.101/24 を追加
     * IPv6 を Ignore
     * Automatically connect を ON

   設定完了したら、変更の反映を忘れないこと。

   ```
   # service NetworkManager restart
   # service network restart
   ```

   SSH できることの確認は、Mac から以下を実行。

   ```
   $ ssh root@192.168.56.101
   ```

1. SELinux を無効化する。  

   ```
   # setenforce 0
   # vi /etc/selinux/config  (*1)
   ...
   SELINUX=disabled
   ```

   (*1) 再起動してもSELinuxが無効化されるよう設定。  

   ＜メモ＞  
   SELinux を無効化しない場合、  
   /var/www/html 配下のマウントポイントあるいはマウントポイントへのシンボリックリンクに対して  
   SELinux 側の設定変更なしには
   Webブラウザからアクセスできない（Forbidden が表示される）模様。  

### 2. PHP 7.4 インストール

以下のコマンドを実行してインストール。

```
# yum install -y http://rpms.famillecollet.com/enterprise/remi-release-7.rpm
# yum install -y yum-utils
# yum-config-manager --enable remi-php74
# yum install -y php74 php74-php php74-php-pdo php74-php-mysqlnd    (*1)
# php74 -v
PHP 7.4.11 (cli) (built: Sep 29 2020 10:17:06) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
```

(*1) *-pdo, *-mysqlnd は PDO を使用するために必要。

### 3. Apache 設定

PHP と同時にインストールされるため、インストール作業はなし。

```
# httpd -v
Server version: Apache/2.4.6 (CentOS)
Server built:   Apr  2 2020 13:13:23
```

以下の手順で Mac から Web アクセスできるよう設定する。

1. HTTPポート（80）を開ける。

   ```
   # firewall-cmd --zone=public --add-service=http --permanent
   # firewall-cmd --reload
   ```

1. Apache HTTP Service を起動する。

   ```
   # service httpd start
   ```

1. Mac のブラウザから http://＜ホストオンリーアダプターに設定したIPアドレス＞ にアクセスして、Webページが表示されることを確認する。

1. 試験用phpファイルを作成する。

   ```
   # echo "<?php phpinfo(); ?>" >  /var/www/html/phpinfo.php
   ```

1. Mac のブラウザから http://＜ホストオンリーアダプターに設定したIPアドレス＞/phpinfo.php にアクセスして、PHP ファイルを参照できることを確認する。

### 4. 開発を容易にするための設定

以下を使えるようにする。  

- 開発用の一般ユーザ
  - OS稼働に重要なファイルを壊すことを避けるため、開発作業は一般ユーザで実施したい。
  - 一般ユーザは ssh、sudo を可能とする。
- /var/www/html 配下のフォルダ共有
  - CentOS から Mac のフォルダを参照できるようにする。
- Apache HTTP Service の自動起動

#### 4-1. 開発用の一般ユーザ作成  

1. root ユーザで以下を実行して、ユーザ php を作成する。  

   ```
   # useradd -m -N -g users php
   # grep php /etc/passwd
   php:x:1000:100::/home/php:/bin/bash
   ```
   -m: ホームディレクトリを作成する。  
   -N: ユーザと同じ名前のグループを作成しない。  
   -g: ユーザの所属するグループを指定する。  

1. ユーザのパスワードを設定する。  

   ```
   # passwd php
   ```

1. 以下サイトを参考に、ユーザに sudo を許可する。  

   [CentOSでuserをsudo可能にする](https://qiita.com/Esfahan/items/a159753d156d23baf180)  

   ユーザを wheel グループに追加する方法で実施。

   ```
   # visudo
      
     ## Allows people in group wheel to run all commands
     %wheel  ALL=(ALL)       ALL

     ## Same thing without a password
     %wheel  ALL=(ALL)       NOPASSWD: ALL

   # usermod -aG wheel php
   ```

1. 念のため root からの ssh 接続を無効化する。  

   ```
   # vi /etc/ssh/sshd_config
   ...
   PermitRootLogin no
   ...
   # service sshd restart
   ```

1. Mac から ssh 接続できることを確認する。  

   ```
   $ ssh php@192.168.56.101
   ```
   192.168.56.101 は自分の環境のホストオンリーアダプターのネットワークに設定したIPアドレス

1. ユーザから su できることを確認する。  

   ```
   $ su -
   ```

#### 4-2. /var/www/html 配下のフォルダ共有

1. 以下を参考に VirtualBox 側の設定を実施する。

    [【入門編】VirtualBox でホストOS(mac)から ゲストOS(主にCentOS)へ共有フォルダを作成する方法](https://qiita.com/take-ookubo/items/b83fdc624505a9988ff2)

   けっこうハマったので、CentOS 側で実行したコマンドをメモしておきます。
   数字部分は実行環境に左右されます。

   ```
   # yum -y update                     (*1)
   # shutdown -r 0
   # yum install epel-release          (*2)
   # yum install -y bzip2 gcc make kernel-devel kernel-headers dkms gcc-c++
   # curl http://download.virtualbox.org/virtualbox/5.2.6/VBoxGuestAdditions_5.2.6.iso -o VBoxGuestAdditions_5.2.6.iso
   # mount -t iso9660 -o loop VBoxGuestAdditions_5.2.6.iso /media
   # export KERN_DIR=/usr/src/kernels/3.10.0-1127.19.1.el7.x86_64/
   # sh /media/VBoxLinuxAdditions.run  (*3) (*4)
   Verifying archive integrity... All good.
   Uncompressing VirtualBox 5.2.6 Guest Additions for Linux........
   VirtualBox Guest Additions installer
   Removing installed version 5.2.6 of VirtualBox Guest Additions...
   Copying additional installer modules ...
   Installing additional modules ...
   VirtualBox Guest Additions: Building the VirtualBox Guest Additions kernel modules.
   VirtualBox Guest Additions: Look at /var/log/vboxadd-setup.log to find out what went wrong
   VirtualBox Guest Additions: Running kernel modules will not be replaced until the system is restarted
   VirtualBox Guest Additions: Starting.
   ```

   (*1) いろいろとバージョンを合わせるのが面倒なので、一気に最新化。
   [VirtualBox上のCentOSにGuestAdditionsをインストール時のエラーについて](http://kuroneko-mikan.hatenablog.com/entry/2015/02/20/211622)

   (*2) dkms インストールに必要。
   [dkms prblem Install CUDA toolkit8](https://qiita.com/sikeda107/items/e916fbb07ca5c3fe60da)

   (*3) 上記が標準出力に表示されなければ、/var/log/vboxadd-install.log を参照して原因を探る。

   (*4) 以下のエラーが発生していたが、もう1回実行したらうまくいった（原因不明）。  

   ```
   In file included from /tmp/vbox.0/hgsmi_base.c:27:0:
   /tmp/vbox.0/vbox_drv.h:124:31: エラー: フィールド ‘mem_global_ref’ が不完全型を持っています
      struct drm_global_reference mem_global_ref;
                                  ^
   /tmp/vbox.0/vbox_drv.h:125:28: エラー: フィールド ‘bo_global_ref’ が不完全型を持っています
      struct ttm_bo_global_ref bo_global_ref;
                               ^
   /tmp/vbox.0/vbox_drv.h:195:21: エラー: フィールド ‘base’ が不完全型を持っています
     struct drm_encoder base;
   ```

1. /var/www/html 配下にマウントポイントを作成する。

   ```
   $ mkdir /var/www/html/questionnaire
   ```

1. 共有フォルダを一般ユーザがアクセスできるパーミッションでマウントする。

   ```
   $ sudo mount -t vboxsf -o uid=$(id apache -u),gid=$(id php -g),fmode=0664,dmode=0775 src /var/www/html/questionnaire
   $ ls -ld /var/www/html/questionnaire
   drwxrwxr-x. 1 apache users 102 10月  3 15:58 /var/www/html/questionnaire
   ```
   src は自分の環境で設定した共有フォルダ名。

1. 再起動後も自動でマウントされるように...ならない！**（未解決）**。  

   ```
   $ sudo vi /etc/fstab
   ...
   src /var/www/html/questionnaire vboxsf defaults,uid=48,gid=100,fmode=0664,dmode=0775 0 0
   $ sudo umount /var/www/html/questionnaire
   $ sudo mount -a
   $ df -hT
   ...
   src                     vboxsf     931G  177G  755G   19% /var/www/html/questionnaire
   ```
   uid と gid は環境によって値が異なる。

   ＜メモ＞  
   再起動してマウントが失敗し、CentOS ごと起動しなかった。   
   SELinux 無効化後は、CentOS が起動したが、上記の自動マウントはされなかった。  

#### 4-3. Apache HTTP Service の自動起動

1. 現在の設定を確認。

   ```
   $ sudo systemctl list-unit-files -t service | grep httpd
   httpd.service                                 disabled
   ```

1. 自動起動を ON にする。

   ```
   $ sudo systemctl enable httpd.service
   $ sudo systemctl list-unit-files -t service | grep httpd
   httpd.service                                 enabled 
   ```

### 5. MariaDB インストール

1. バージョンを指定してインストールするため、yumリポジトリの設定ファイルを書き換えてインストールする。  
    公式サイト[Installing MariaDB with yum/dnf](https://mariadb.com/kb/en/yum/#pinning-the-mariadb-repository-to-a-specific-minor-release)

   ```
   $ curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash
   $ sudo vi /etc/yum.repos.d/MariaDB.repo
   [mariadb]
   name = MariaDB-10.5.6
   baseurl=http://yum.mariadb.org/10.5.6/centos/7/x86_64
   gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
   gpgcheck=1
   $ sudo yum clean all
   $ sudo rpm --import https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
   $ sudo yum install -y MariaDB-server galera-4 MariaDB-client MariaDB-shared MariaDB-backup MariaDB-common
   ```

1. 日本語と4byte文字列を扱えるよう、文字コードの設定を追加する。  

   ```
   $ sudo vi /etc/my.cnf.d/server.cnf 
   [mariadb]
   character-set-server=utf8mb4
   ```

   参考：[MySQL で utf8 と utf8mb4 の混在で起きること](https://tmtms.hatenablog.com/entry/2016/09/06/mysql-utf8)

1. MariaDB の自動起動を有効化して、起動する。  

   ```
   $ sudo systemctl enable mariadb
   $ sudo systemctl start mariadb
   ```

1. mysql コマンドを実行して MariaDB に入れることを確認する。  
   ついでに設定した文字コードを確認する。  

   ```
   $ sudo mysql -u root -h localhost
   > SHOW variables LIKE 'char%';
   +--------------------------+----------------------------+
   | Variable_name            | Value                      |
   +--------------------------+----------------------------+
   | character_set_client     | utf8                       |
   | character_set_connection | utf8                       |
   | character_set_database   | utf8mb4                    |
   | character_set_filesystem | binary                     |
   | character_set_results    | utf8                       |
   | character_set_server     | utf8mb4                    |
   | character_set_system     | utf8                       |
   | character_sets_dir       | /usr/share/mysql/charsets/ |
   +--------------------------+----------------------------+
   8 rows in set (0.001 sec)
   ```

1. DBとユーザーを作成する。 
   大文字小文字を区別するため、比較方法はバイナリとする。  

   ```
   > CREATE DATABASE questionnaire COLLATE utf8mb4_bin;
   > CREATE USER php@localhost;
   > GRANT all ON questionnaire.* TO php@localhost;
   > FLUSH PRIVILEGES;
   ```

1. 作成したユーザーでDBの内容を閲覧できることを確認する。

   ```
   > quit;
   $ mysql -u php -h localhost
   > use questionnaire;
   > show tables;
   ```