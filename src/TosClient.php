<?php
/**
 * Copyright (2022) Volcengine
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Tos;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tos\Config\ConfigParser;
use Tos\Exception\TosClientException;
use Tos\Exception\TosServerException;
use Tos\Helper\Helper;
use Tos\Model\AbortMultipartUploadInput;
use Tos\Model\AbortMultipartUploadOutput;
use Tos\Model\AppendObjectInput;
use Tos\Model\AppendObjectOutput;
use Tos\Model\CompleteMultipartUploadInput;
use Tos\Model\CompleteMultipartUploadOutput;
use Tos\Model\Constant;
use Tos\Model\CopyObjectInput;
use Tos\Model\CopyObjectOutput;
use Tos\Model\CreateBucketInput;
use Tos\Model\CreateBucketOutput;
use Tos\Model\CreateMultipartUploadInput;
use Tos\Model\CreateMultipartUploadOutput;
use Tos\Model\DeleteBucketInput;
use Tos\Model\DeleteBucketOutput;
use Tos\Model\DeleteMultiObjectsInput;
use Tos\Model\DeleteMultiObjectsOutput;
use Tos\Model\DeleteObjectInput;
use Tos\Model\DeleteObjectOutput;
use Tos\Model\DeleteObjectTaggingInput;
use Tos\Model\DeleteObjectTaggingOutput;
use Tos\Model\GetObjectACLInput;
use Tos\Model\GetObjectACLOutput;
use Tos\Model\GetObjectInput;
use Tos\Model\GetObjectOutput;
use Tos\Model\GetObjectTaggingInput;
use Tos\Model\GetObjectTaggingOutput;
use Tos\Model\GetObjectToFileInput;
use Tos\Model\GetObjectToFileOutput;
use Tos\Model\HeadBucketInput;
use Tos\Model\HeadBucketOutput;
use Tos\Model\HeadObjectInput;
use Tos\Model\HeadObjectOutput;
use Tos\Model\HttpRequest;
use Tos\Model\ListBucketsInput;
use Tos\Model\ListBucketsOutput;
use Tos\Model\ListMultipartUploadsInput;
use Tos\Model\ListMultipartUploadsOutput;
use Tos\Model\ListObjectsInput;
use Tos\Model\ListObjectsOutput;
use Tos\Model\ListObjectVersionsInput;
use Tos\Model\ListObjectVersionsOutput;
use Tos\Model\ListPartsInput;
use Tos\Model\ListPartsOutput;
use Tos\Model\PreSignedURLInput;
use Tos\Model\PreSignedURLOutput;
use Tos\Model\PutObjectACLInput;
use Tos\Model\PutObjectACLOutput;
use Tos\Model\PutObjectFromFileInput;
use Tos\Model\PutObjectFromFileOutput;
use Tos\Model\PutObjectInput;
use Tos\Model\PutObjectOutput;
use Tos\Model\PutObjectTaggingInput;
use Tos\Model\PutObjectTaggingOutput;
use Tos\Model\SetObjectMetaInput;
use Tos\Model\SetObjectMetaOutput;
use Tos\Model\UploadPartCopyInput;
use Tos\Model\UploadPartCopyOutput;
use Tos\Model\UploadPartFromFileInput;
use Tos\Model\UploadPartFromFileOutput;
use Tos\Model\UploadPartInput;
use Tos\Model\UploadPartOutput;


/**
 * @method ListBucketsOutput listBuckets(ListBucketsInput $input);
 * @method CreateBucketOutput createBucket(CreateBucketInput $input);
 * @method HeadBucketOutput headBucket(HeadBucketInput $input);
 * @method DeleteBucketOutput deleteBucket(DeleteBucketInput $input);
 * @method CopyObjectOutput copyObject(CopyObjectInput $input);
 * @method DeleteObjectOutput deleteObject(DeleteObjectInput $input);
 * @method DeleteMultiObjectsOutput deleteMultiObjects(DeleteMultiObjectsInput $input);
 * @method GetObjectOutput getObject(GetObjectInput $input);
 * @method GetObjectToFileOutput getObjectToFile(GetObjectToFileInput $input);
 * @method GetObjectACLOutput getObjectACL(GetObjectACLInput $input);
 * @method HeadObjectOutput headObject(HeadObjectInput $input);
 * @method AppendObjectOutput appendObject(AppendObjectInput $input);
 * @method ListObjectsOutput listObjects(ListObjectsInput $input);
 * @method ListObjectVersionsOutput listObjectVersions(ListObjectVersionsInput $input);
 * @method PutObjectOutput putObject(PutObjectInput $input);
 * @method PutObjectFromFileOutput putObjectFromFile(PutObjectFromFileInput $input);
 * @method PutObjectACLOutput putObjectACL(PutObjectACLInput $input);
 * @method SetObjectMetaOutput setObjectMeta(SetObjectMetaInput $input);
 * @method CreateMultipartUploadOutput createMultipartUpload(CreateMultipartUploadInput $input);
 * @method UploadPartOutput uploadPart(UploadPartInput $input);
 * @method UploadPartFromFileOutput uploadPartFromFile(UploadPartFromFileInput $input);
 * @method CompleteMultipartUploadOutput completeMultipartUpload(CompleteMultipartUploadInput $input);
 * @method AbortMultipartUploadOutput abortMultipartUpload(AbortMultipartUploadInput $input);
 * @method UploadPartCopyOutput uploadPartCopy(UploadPartCopyInput $input);
 * @method ListMultipartUploadsOutput listMultipartUploads(ListMultipartUploadsInput $input);
 * @method ListPartsOutput listParts(ListPartsInput $input);
 * @method PutObjectTaggingOutput putObjectTagging(PutObjectTaggingInput $input);
 * @method GetObjectTaggingOutput getObjectTagging(GetObjectTaggingInput $input);
 * @method DeleteObjectTaggingOutput deleteObjectTagging(DeleteObjectTaggingInput $input);
 *
 */
class TosClient
{

    /**
     * @var ConfigParser
     */
    private $cp;

    /**
     * @var Client
     */
    private $client;


    use Model\InputTranslator;
    use Model\OutputParser;
    use Auth\Signer;
    use Upload\Uploader;

    private static $mimeTypes = [
        '3gp' => 'video/3gpp',
        '7z' => 'application/x-7z-compressed',
        'abw' => 'application/x-abiword',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'alc' => 'chemical/x-alchemy',
        'amr' => 'audio/amr',
        'anx' => 'application/annodex',
        'apk' => 'application/vnd.android.package-archive',
        'appcache' => 'text/cache-manifest',
        'art' => 'image/x-jg',
        'asc' => 'text/plain',
        'asf' => 'video/x-ms-asf',
        'aso' => 'chemical/x-ncbi-asn1-binary',
        'asx' => 'video/x-ms-asf',
        'atom' => 'application/atom+xml',
        'atomcat' => 'application/atomcat+xml',
        'atomsrv' => 'application/atomserv+xml',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'awb' => 'audio/amr-wb',
        'axa' => 'audio/annodex',
        'axv' => 'video/annodex',
        'b' => 'chemical/x-molconn-Z',
        'bak' => 'application/x-trash',
        'bat' => 'application/x-msdos-program',
        'bcpio' => 'application/x-bcpio',
        'bib' => 'text/x-bibtex',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/x-ms-bmp',
        'boo' => 'text/x-boo',
        'book' => 'application/x-maker',
        'brf' => 'text/plain',
        'bsd' => 'chemical/x-crossfire',
        'c' => 'text/x-csrc',
        'c++' => 'text/x-c++src',
        'c3d' => 'chemical/x-chem3d',
        'cab' => 'application/x-cab',
        'cac' => 'chemical/x-cache',
        'cache' => 'chemical/x-cache',
        'cap' => 'application/vnd.tcpdump.pcap',
        'cascii' => 'chemical/x-cactvs-binary',
        'cat' => 'application/vnd.ms-pki.seccat',
        'cbin' => 'chemical/x-cactvs-binary',
        'cbr' => 'application/x-cbr',
        'cbz' => 'application/x-cbz',
        'cc' => 'text/x-c++src',
        'cda' => 'application/x-cdf',
        'cdf' => 'application/x-cdf',
        'cdr' => 'image/x-coreldraw',
        'cdt' => 'image/x-coreldrawtemplate',
        'cdx' => 'chemical/x-cdx',
        'cdy' => 'application/vnd.cinderella',
        'cef' => 'chemical/x-cxf',
        'cer' => 'chemical/x-cerius',
        'chm' => 'chemical/x-chemdraw',
        'chrt' => 'application/x-kchart',
        'cif' => 'chemical/x-cif',
        'class' => 'application/java-vm',
        'cls' => 'text/x-tex',
        'cmdf' => 'chemical/x-cmdf',
        'cml' => 'chemical/x-cml',
        'cod' => 'application/vnd.rim.cod',
        'com' => 'application/x-msdos-program',
        'cpa' => 'chemical/x-compass',
        'cpio' => 'application/x-cpio',
        'cpp' => 'text/x-c++src',
        'cpt' => 'application/mac-compactpro',
        'cr2' => 'image/x-canon-cr2',
        'crl' => 'application/x-pkcs7-crl',
        'crt' => 'application/x-x509-ca-cert',
        'crw' => 'image/x-canon-crw',
        'csd' => 'audio/csound',
        'csf' => 'chemical/x-cache-csf',
        'csh' => 'application/x-csh',
        'csm' => 'chemical/x-csml',
        'csml' => 'chemical/x-csml',
        'css' => 'text/css',
        'csv' => 'text/csv',
        'ctab' => 'chemical/x-cactvs-binary',
        'ctx' => 'chemical/x-ctx',
        'cu' => 'application/cu-seeme',
        'cub' => 'chemical/x-gaussian-cube',
        'cxf' => 'chemical/x-cxf',
        'cxx' => 'text/x-c++src',
        'd' => 'text/x-dsrc',
        'davmount' => 'application/davmount+xml',
        'dcm' => 'application/dicom',
        'dcr' => 'application/x-director',
        'ddeb' => 'application/vnd.debian.binary-package',
        'dif' => 'video/dv',
        'diff' => 'text/x-diff',
        'dir' => 'application/x-director',
        'djv' => 'image/vnd.djvu',
        'djvu' => 'image/vnd.djvu',
        'dl' => 'video/dl',
        'dll' => 'application/x-msdos-program',
        'dmg' => 'application/x-apple-diskimage',
        'dms' => 'application/x-dms',
        'doc' => 'application/msword',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dot' => 'application/msword',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dv' => 'video/dv',
        'dvi' => 'application/x-dvi',
        'dx' => 'chemical/x-jcamp-dx',
        'dxr' => 'application/x-director',
        'emb' => 'chemical/x-embl-dl-nucleotide',
        'embl' => 'chemical/x-embl-dl-nucleotide',
        'eml' => 'message/rfc822',
        'eot' => 'application/vnd.ms-fontobject',
        'eps' => 'application/postscript',
        'eps2' => 'application/postscript',
        'eps3' => 'application/postscript',
        'epsf' => 'application/postscript',
        'epsi' => 'application/postscript',
        'erf' => 'image/x-epson-erf',
        'es' => 'application/ecmascript',
        'etx' => 'text/x-setext',
        'exe' => 'application/x-msdos-program',
        'ez' => 'application/andrew-inset',
        'fb' => 'application/x-maker',
        'fbdoc' => 'application/x-maker',
        'fch' => 'chemical/x-gaussian-checkpoint',
        'fchk' => 'chemical/x-gaussian-checkpoint',
        'fig' => 'application/x-xfig',
        'flac' => 'audio/flac',
        'fli' => 'video/fli',
        'flv' => 'video/x-flv',
        'fm' => 'application/x-maker',
        'frame' => 'application/x-maker',
        'frm' => 'application/x-maker',
        'gal' => 'chemical/x-gaussian-log',
        'gam' => 'chemical/x-gamess-input',
        'gamin' => 'chemical/x-gamess-input',
        'gan' => 'application/x-ganttproject',
        'gau' => 'chemical/x-gaussian-input',
        'gcd' => 'text/x-pcs-gcd',
        'gcf' => 'application/x-graphing-calculator',
        'gcg' => 'chemical/x-gcg8-sequence',
        'gen' => 'chemical/x-genbank',
        'gf' => 'application/x-tex-gf',
        'gif' => 'image/gif',
        'gjc' => 'chemical/x-gaussian-input',
        'gjf' => 'chemical/x-gaussian-input',
        'gl' => 'video/gl',
        'gnumeric' => 'application/x-gnumeric',
        'gpt' => 'chemical/x-mopac-graph',
        'gsf' => 'application/x-font',
        'gsm' => 'audio/x-gsm',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/gzip',
        'h' => 'text/x-chdr',
        'h++' => 'text/x-c++hdr',
        'hdf' => 'application/x-hdf',
        'hh' => 'text/x-c++hdr',
        'hin' => 'chemical/x-hin',
        'hpp' => 'text/x-c++hdr',
        'hqx' => 'application/mac-binhex40',
        'hs' => 'text/x-haskell',
        'hta' => 'application/hta',
        'htc' => 'text/x-component',
        'htm' => 'text/html',
        'html' => 'text/html',
        'hwp' => 'application/x-hwp',
        'hxx' => 'text/x-c++hdr',
        'ica' => 'application/x-ica',
        'ice' => 'x-conference/x-cooltalk',
        'ico' => 'image/vnd.microsoft.icon',
        'ics' => 'text/calendar',
        'icz' => 'text/calendar',
        'ief' => 'image/ief',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'iii' => 'application/x-iphone',
        'info' => 'application/x-info',
        'inp' => 'chemical/x-gamess-input',
        'ins' => 'application/x-internet-signup',
        'iso' => 'application/x-iso9660-image',
        'isp' => 'application/x-internet-signup',
        'ist' => 'chemical/x-isostar',
        'istr' => 'chemical/x-isostar',
        'jad' => 'text/vnd.sun.j2me.app-descriptor',
        'jam' => 'application/x-jam',
        'jar' => 'application/java-archive',
        'java' => 'text/x-java',
        'jdx' => 'chemical/x-jcamp-dx',
        'jmz' => 'application/x-jmol',
        'jng' => 'image/x-jng',
        'jnlp' => 'application/x-java-jnlp-file',
        'jp2' => 'image/jp2',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpf' => 'image/jpx',
        'jpg' => 'image/jpeg',
        'jpg2' => 'image/jp2',
        'jpm' => 'image/jpm',
        'jpx' => 'image/jpx',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'kar' => 'audio/midi',
        'key' => 'application/pgp-keys',
        'kil' => 'application/x-killustrator',
        'kin' => 'chemical/x-kinemage',
        'kml' => 'application/vnd.google-earth.kml+xml',
        'kmz' => 'application/vnd.google-earth.kmz',
        'kpr' => 'application/x-kpresenter',
        'kpt' => 'application/x-kpresenter',
        'ksp' => 'application/x-kspread',
        'kwd' => 'application/x-kword',
        'kwt' => 'application/x-kword',
        'latex' => 'application/x-latex',
        'lha' => 'application/x-lha',
        'lhs' => 'text/x-literate-haskell',
        'lin' => 'application/bbolin',
        'lsf' => 'video/x-la-asf',
        'lsx' => 'video/x-la-asf',
        'ltx' => 'text/x-tex',
        'ly' => 'text/x-lilypond',
        'lyx' => 'application/x-lyx',
        'lzh' => 'application/x-lzh',
        'lzx' => 'application/x-lzx',
        'm3g' => 'application/m3g',
        'm3u' => 'audio/x-mpegurl',
        'm3u8' => 'application/x-mpegURL',
        'm4a' => 'audio/mpeg',
        'maker' => 'application/x-maker',
        'man' => 'application/x-troff-man',
        'mbox' => 'application/mbox',
        'mcif' => 'chemical/x-mmcif',
        'mcm' => 'chemical/x-macmolecule',
        'mdb' => 'application/msaccess',
        'me' => 'application/x-troff-me',
        'mesh' => 'model/mesh',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mif' => 'application/x-mif',
        'mkv' => 'video/x-matroska',
        'mm' => 'application/x-freemind',
        'mmd' => 'chemical/x-macromodel-input',
        'mmf' => 'application/vnd.smaf',
        'mml' => 'text/mathml',
        'mmod' => 'chemical/x-macromodel-input',
        'mng' => 'video/x-mng',
        'moc' => 'text/x-moc',
        'mol' => 'chemical/x-mdl-molfile',
        'mol2' => 'chemical/x-mol2',
        'moo' => 'chemical/x-mopac-out',
        'mop' => 'chemical/x-mopac-input',
        'mopcrt' => 'chemical/x-mopac-input',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'mpc' => 'chemical/x-mopac-input',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpega' => 'audio/mpeg',
        'mpg' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'mph' => 'application/x-comsol',
        'mpv' => 'video/x-matroska',
        'ms' => 'application/x-troff-ms',
        'msh' => 'model/mesh',
        'msi' => 'application/x-msi',
        'mvb' => 'chemical/x-mopac-vib',
        'mxf' => 'application/mxf',
        'mxu' => 'video/vnd.mpegurl',
        'nb' => 'application/mathematica',
        'nbp' => 'application/mathematica',
        'nc' => 'application/x-netcdf',
        'nef' => 'image/x-nikon-nef',
        'nwc' => 'application/x-nwc',
        'o' => 'application/x-object',
        'oda' => 'application/oda',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'oga' => 'audio/ogg',
        'ogg' => 'audio/ogg',
        'ogv' => 'video/ogg',
        'ogx' => 'application/ogg',
        'old' => 'application/x-trash',
        'one' => 'application/onenote',
        'onepkg' => 'application/onenote',
        'onetmp' => 'application/onenote',
        'onetoc2' => 'application/onenote',
        'opf' => 'application/oebps-package+xml',
        'opus' => 'audio/ogg',
        'orc' => 'audio/csound',
        'orf' => 'image/x-olympus-orf',
        'otf' => 'application/font-sfnt',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oza' => 'application/x-oz-application',
        'p' => 'text/x-pascal',
        'p7r' => 'application/x-pkcs7-certreqresp',
        'pac' => 'application/x-ns-proxy-autoconfig',
        'pas' => 'text/x-pascal',
        'pat' => 'image/x-coreldrawpattern',
        'patch' => 'text/x-diff',
        'pbm' => 'image/x-portable-bitmap',
        'pcap' => 'application/vnd.tcpdump.pcap',
        'pcf' => 'application/x-font-pcf',
        'pcf.Z' => 'application/x-font-pcf',
        'pcx' => 'image/pcx',
        'pdb' => 'chemical/x-pdb',
        'pdf' => 'application/pdf',
        'pfa' => 'application/x-font',
        'pfb' => 'application/x-font',
        'pfr' => 'application/font-tdpfr',
        'pgm' => 'image/x-portable-graymap',
        'pgn' => 'application/x-chess-pgn',
        'pgp' => 'application/pgp-encrypted',
        'php' => '#application/x-httpd-php',
        'php3' => '#application/x-httpd-php3',
        'php3p' => '#application/x-httpd-php3-preprocessed',
        'php4' => '#application/x-httpd-php4',
        'php5' => '#application/x-httpd-php5',
        'phps' => '#application/x-httpd-php-source',
        'pht' => '#application/x-httpd-php',
        'phtml' => '#application/x-httpd-php',
        'pk' => 'application/x-tex-pk',
        'pl' => 'text/x-perl',
        'pls' => 'audio/x-scpls',
        'pm' => 'text/x-perl',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'pot' => 'text/plain',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'prf' => 'application/pics-rules',
        'prt' => 'chemical/x-ncbi-asn1-ascii',
        'ps' => 'application/postscript',
        'psd' => 'image/x-photoshop',
        'py' => 'text/x-python',
        'pyc' => 'application/x-python-code',
        'pyo' => 'application/x-python-code',
        'qgs' => 'application/x-qgis',
        'qt' => 'video/quicktime',
        'qtl' => 'application/x-quicktimeplayer',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rar' => 'application/rar',
        'ras' => 'image/x-cmu-raster',
        'rb' => 'application/x-ruby',
        'rd' => 'chemical/x-mdl-rdfile',
        'rdf' => 'application/rdf+xml',
        'rdp' => 'application/x-rdp',
        'rgb' => 'image/x-rgb',
        'rhtml' => '#application/x-httpd-eruby',
        'rm' => 'audio/x-pn-realaudio',
        'roff' => 'application/x-troff',
        'ros' => 'chemical/x-rosdal',
        'rpm' => 'application/x-redhat-package-manager',
        'rss' => 'application/x-rss+xml',
        'rtf' => 'application/rtf',
        'rtx' => 'text/richtext',
        'rxn' => 'chemical/x-mdl-rxnfile',
        'scala' => 'text/x-scala',
        'sce' => 'application/x-scilab',
        'sci' => 'application/x-scilab',
        'sco' => 'audio/csound',
        'scr' => 'application/x-silverlight',
        'sct' => 'text/scriptlet',
        'sd' => 'chemical/x-mdl-sdfile',
        'sd2' => 'audio/x-sd2',
        'sda' => 'application/vnd.stardivision.draw',
        'sdc' => 'application/vnd.stardivision.calc',
        'sdd' => 'application/vnd.stardivision.impress',
        'sds' => 'application/vnd.stardivision.chart',
        'sdw' => 'application/vnd.stardivision.writer',
        'ser' => 'application/java-serialized-object',
        'sfd' => 'application/vnd.font-fontforge-sfd',
        'sfv' => 'text/x-sfv',
        'sgf' => 'application/x-go-sgf',
        'sgl' => 'application/vnd.stardivision.writer-global',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'shp' => 'application/x-qgis',
        'shtml' => 'text/html',
        'shx' => 'application/x-qgis',
        'sid' => 'audio/prs.sid',
        'sig' => 'application/pgp-signature',
        'sik' => 'application/x-trash',
        'silo' => 'model/mesh',
        'sis' => 'application/vnd.symbian.install',
        'sisx' => 'x-epoc/x-sisx-app',
        'sit' => 'application/x-stuffit',
        'sitx' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'smi' => 'application/smil+xml',
        'smil' => 'application/smil+xml',
        'snd' => 'audio/basic',
        'spc' => 'chemical/x-galactic-spc',
        'spl' => 'application/x-futuresplash',
        'spx' => 'audio/ogg',
        'sql' => 'application/x-sql',
        'src' => 'application/x-wais-source',
        'srt' => 'text/plain',
        'stc' => 'application/vnd.sun.xml.calc.template',
        'std' => 'application/vnd.sun.xml.draw.template',
        'sti' => 'application/vnd.sun.xml.impress.template',
        'stw' => 'application/vnd.sun.xml.writer.template',
        'sty' => 'text/x-tex',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'sw' => 'chemical/x-swissprot',
        'swf' => 'application/x-shockwave-flash',
        'swfl' => 'application/x-shockwave-flash',
        'sxc' => 'application/vnd.sun.xml.calc',
        'sxd' => 'application/vnd.sun.xml.draw',
        'sxg' => 'application/vnd.sun.xml.writer.global',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sxm' => 'application/vnd.sun.xml.math',
        'sxw' => 'application/vnd.sun.xml.writer',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'taz' => 'application/x-gtar-compressed',
        'tcl' => 'application/x-tcl',
        'tex' => 'text/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'text' => 'text/plain',
        'tgf' => 'chemical/x-mdl-tgf',
        'tgz' => 'application/x-gtar-compressed',
        'thmx' => 'application/vnd.ms-officetheme',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tk' => 'text/x-tcl',
        'tm' => 'text/texmacs',
        'torrent' => 'application/x-bittorrent',
        'tr' => 'application/x-troff',
        'ts' => 'video/MP2T',
        'tsp' => 'application/dsptype',
        'tsv' => 'text/tab-separated-values',
        'ttf' => 'application/font-sfnt',
        'ttl' => 'text/turtle',
        'txt' => 'text/plain',
        'uls' => 'text/iuls',
        'ustar' => 'application/x-ustar',
        'val' => 'chemical/x-ncbi-asn1-binary',
        'vcard' => 'text/vcard',
        'vcd' => 'application/x-cdlink',
        'vcf' => 'text/vcard',
        'vcs' => 'text/x-vcalendar',
        'vmd' => 'chemical/x-vmd',
        'vms' => 'chemical/x-vamas-iso14976',
        'vrm' => 'x-world/x-vrml',
        'vrml' => 'model/vrml',
        'vsd' => 'application/vnd.visio',
        'vss' => 'application/vnd.visio',
        'vst' => 'application/vnd.visio',
        'vsw' => 'application/vnd.visio',
        'wad' => 'application/x-doom',
        'wasm' => 'application/wasm',
        'wav' => 'audio/x-wav',
        'wax' => 'audio/x-ms-wax',
        'wbmp' => 'image/vnd.wap.wbmp',
        'wbxml' => 'application/vnd.wap.wbxml',
        'webm' => 'video/webm',
        'wk' => 'application/x-123',
        'wm' => 'video/x-ms-wm',
        'wma' => 'audio/x-ms-wma',
        'wmd' => 'application/x-ms-wmd',
        'wml' => 'text/vnd.wap.wml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wmz' => 'application/x-ms-wmz',
        'woff' => 'application/font-woff',
        'wp5' => 'application/vnd.wordperfect5.1',
        'wpd' => 'application/vnd.wordperfect',
        'wrl' => 'model/vrml',
        'wsc' => 'text/scriptlet',
        'wvx' => 'video/x-ms-wvx',
        'wz' => 'application/x-wingz',
        'x3d' => 'model/x3d+xml',
        'x3db' => 'model/x3d+binary',
        'x3dv' => 'model/x3d+vrml',
        'xbm' => 'image/x-xbitmap',
        'xcf' => 'application/x-xcf',
        'xcos' => 'application/x-scilab-xcos',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlb' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlt' => 'application/vnd.ms-excel',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xml' => 'application/xml',
        'xpi' => 'application/x-xpinstall',
        'xpm' => 'image/x-xpixmap',
        'xsd' => 'application/xml',
        'xsl' => 'application/xslt+xml',
        'xslt' => 'application/xslt+xml',
        'xspf' => 'application/xspf+xml',
        'xtel' => 'chemical/x-xtel',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'xwd' => 'image/x-xwindowdump',
        'xyz' => 'chemical/x-xyz',
        'xz' => 'application/x-xz',
        'zip' => 'application/zip',
    ];

    /**
     * @param array|ConfigParser|string $configOrRegion
     */
    public function __construct($configOrRegion = 'cn-beijing', $ak = '', $sk = '', $endpoint = '')
    {
        if (is_array($configOrRegion)) {
            $this->cp = new ConfigParser($configOrRegion);
        } else if ($configOrRegion instanceof ConfigParser) {
            $this->cp = $configOrRegion;
        } else if (is_string($configOrRegion)) {
            $this->cp = new ConfigParser([
                'region' => trim($configOrRegion),
                'ak' => $ak,
                'sk' => $sk,
                'endpoint' => $endpoint,
            ]);
        } else {
            throw new TosClientException('invalid config');
        }

        $this->client = new Client([
            'timeout' => 0,
            'read_timeout' => $this->cp->getSocketTimeout() / 1000,
            'connect_timeout' => $this->cp->getConnectionTimeout() / 1000,
            'allow_redirects' => false,
            'verify' => $this->cp->isEnableVerifySSL(),
            'http_errors' => false,
            'decode_content' => false,
        ]);
    }

    public function __call($method, $args)
    {
        $method = ucfirst($method);
        $input = null;
        if (count($args) === 0) {
            if ($method === 'ListBuckets') {
                $input = new ListBucketsInput();
            }
        } else {
            $input = $args[0];
        }

        $transFn = 'trans' . $method . 'Input';
        $parseFn = 'parse' . $method . 'Output';
        $request = null;
        if (is_callable(__CLASS__ . '::' . $transFn) && is_callable(__CLASS__ . '::' . $parseFn)) {
            $body = null;
            $closeBody = false;
            try {
                if ($method === 'GetObjectToFile') {
                    list($request, $filePath, $doMkdir, $bucket, $key) = self::$transFn($input, $this->cp);
                    $response = $this->doRequest($request, !$doMkdir && $input->isStreamMode());
                    return self::$parseFn($response, $filePath, $doMkdir, $bucket, $key, $input->isStreamMode());
                }

                if ($method === 'GetObject') {
                    $request = self::$transFn($input, $this->cp);
                    $response = $this->doRequest($request, $input->isStreamMode());
                    return self::$parseFn($response, $input->isStreamMode());
                }

                $request = self::$transFn($input, $this->cp);
                if ($method === 'PutObjectFromFile' || $method === 'UploadPartFromFile') {
                    $closeBody = true;
                } else if ($method === 'PutObject' || $method === 'AppendObject' || $method === 'UploadPart') {
                    if (!$request->body) {
                        $request->headers[Constant::HeaderContentLength] = 0;
                    }
                }
                $this->autoRecognizeContentType($request, $method);
                $body = $request->body;
                $response = $this->doRequest($request);
                if ($method === 'UploadPart' || $method === 'UploadPartCopy' || $method == 'UploadPartFromFile') {
                    return self::$parseFn($response, $input->getPartNumber());
                }

                return self::$parseFn($response);
            } catch (TosClientException $ex) {
                throw $ex;
            } catch (TosServerException $ex) {
                throw $ex;
            } catch (TransferException $ex) {
                if($request){
                    throw new TosClientException(sprintf('do http request for %s error, %s', $this->cp->getEndpoint($request->bucket, $request->key, '', '', true), $ex->getMessage()), $ex);
                }else{
                    throw new TosClientException(sprintf('do http request error, %s', $ex->getMessage()), $ex);
                }
            } catch (GuzzleException $ex) {
                if($request){
                    throw new TosClientException(sprintf('do http request for %s error, %s', $this->cp->getEndpoint($request->bucket, $request->key, '', '', true), $ex->getMessage()), $ex);
                }else{
                    throw new TosClientException(sprintf('do http request error, %s', $ex->getMessage()), $ex);
                }
            } catch (\Exception $ex) {
                throw new TosClientException(sprintf('unknown error, %s', $ex->getMessage()), $ex);
            } finally {
                if ($closeBody && $body) {
                    if (is_resource($body)) {
                        fclose($body);
                    } else if ($body instanceof StreamInterface) {
                        $body->close();
                    }
                }
            }
        }
        throw new TosClientException(sprintf('unknown method %s error', $method));
    }

    private function autoRecognizeContentType(HttpRequest &$request, $method)
    {
        if (!$this->cp->isAutoRecognizeContentType()) {
            return;
        }
        if (isset($request->headers[Constant::HeaderContentType]) || !$request->key) {
            return;
        }

        if ($method === 'PutObject' || $method === 'PutObjectFromFile' || $method === 'AppendObject'
            || $method === 'SetObjectMeta' || $method === 'CreateMultipartUpload' || $method === 'UploadFile' || $method === 'ResumableCopyObject') {
            $pos = strrpos($request->key, '.');
            if (is_int($pos) && $pos >= 0) {
                $key = strtolower(substr($request->key, $pos + 1));
                if (isset(self::$mimeTypes[$key])) {
                    $request->headers[Constant::HeaderContentType] = self::$mimeTypes[$key];
                }
            }
        }
    }

    /**
     * @param HttpRequest $request
     * @param bool $stream
     * @return ResponseInterface
     */
    private function &doRequest(HttpRequest &$request, $stream = false)
    {
        $response = $this->doRequestAsync($request, $stream)->wait();
        return $response;
    }

    private function &doRequestAsync(HttpRequest &$request, $stream = false)
    {
        list($method, $requestUri, $headers, $body) = $this->prepareRequest($request);
        $options = [
            'headers' => $headers,
            'stream' => $stream,
            'body' => $body,
        ];

        $promise = $this->client->requestAsync($method, $requestUri, $options);
        return $promise;
    }

    private function &prepareRequest(HttpRequest &$request)
    {
        self::sign($request, $this->cp->getHost($request->bucket), $this->cp->getAk(),
            $this->cp->getSk(), $this->cp->getSecurityToken(), $this->cp->getRegion());

        $headers = $request->headers;
        $headers[Constant::HeaderUserAgent] = $this->cp->getUserAgent();
        $headers[Constant::HeaderConnection] = 'Keep-Alive';
        $requestUri = $this->cp->getEndpoint($request->bucket, $request->key, '', '', true);
        $queries = $request->queries;
        $body = $request->body;

        if ($queries && count($queries) > 0) {
            $requestUri .= '?';
            foreach ($queries as $key => $val) {
                $requestUri .= $key . '=' . $val . '&';
            }
            $requestUri = substr($requestUri, 0, strlen($requestUri) - 1);
        }

        if ($body) {
            if (is_resource($body) && ftell($body) > 0) {
                // fix auto rewind bug in CurlFactory->applyBody
                $body = new NoSeekStream(new Stream($body));
            } else if ($body instanceof StreamInterface && $body->tell() > 0) {
                // fix auto rewind bug in CurlFactory->applyBody
                $body = new NoSeekStream($body);
            }
        }

        $result = [$request->method, $requestUri, $headers, $body];
        return $result;
    }

    /**
     * @param PreSignedURLInput $input
     * @return PreSignedURLOutput
     */
    public function &preSignedURL(PreSignedURLInput $input)
    {
        $request = new HttpRequest();
        if (($method = $input->getHttpMethod()) && self::checkHttpMethod($method)) {
            $request->method = $method;
        }

        if ($bucket = $input->getBucket()) {
            $bucket = self::checkBucket($bucket);
            $request->bucket = $bucket;
        }

        if ($key = $input->getKey()) {
            $key = self::checkKey($key);
            $request->key = $key;
        }

        if (($query = $input->getQuery()) && is_array($query)) {
            $request->queries = $query;
        } else {
            $request->queries = [];
        }

        $schema = '';
        $domain = '';
        if ($alternativeEndpoint = $input->getAlternativeEndpoint()) {
            $result = Helper::splitEndpoint($alternativeEndpoint);
            $schema = $result['schema'];
            $domain = $result['domain'];
        }

        $signedHeader = [];
        if (($ak = $this->cp->getAk()) && ($sk = $this->cp->getSk())) {
            if (($header = $input->getHeader()) && is_array($header)) {
                $request->headers = $header;
            } else {
                $request->headers = [];
            }

            $request->headers[Constant::HeaderHost] = $this->cp->getHost($request->bucket, $domain, $input->isCustomDomain());
            $signedHeaders = null;
            list($canonicalHeaders, $contentSha256) = self::getCanonicalHeaders($request, $signedHeaders, $signedHeader);

            $longDate = null;
            $shortDate = null;
            $credentialScope = null;
            $region = $this->cp->getRegion();
            self::prepareDateAndCredentialScope($longDate, $shortDate, $credentialScope, $region);

            $request->queries['X-Tos-Algorithm'] = self::$algorithm;
            $request->queries['X-Tos-Credential'] = $ak . '/' . $credentialScope;
            $request->queries['X-Tos-Date'] = $longDate;
            $request->queries['X-Tos-Expires'] = intval($expires = $input->getExpires()) ? $expires : 3600;
            $request->queries['X-Tos-SignedHeaders'] = $signedHeaders;
            if ($securityToken = $this->cp->getSecurityToken()) {
                $request->queries['X-Tos-Security-Token'] = $securityToken;
            }
            $canonicalRequest = self::getCanonicalRequest($request, $canonicalHeaders, $signedHeaders, true, $contentSha256);

            $stringToSign = self::getStringToSign($canonicalRequest, $longDate, $credentialScope);
            $signature = self::getSignature($stringToSign, $shortDate, $sk, $region);
            $request->queries['X-Tos-Signature'] = rawurlencode($signature);
        }

        $signedUrl = $this->cp->getEndpoint($request->bucket, $request->key, $schema, $domain, true, $input->isCustomDomain());
        if (count($request->queries) > 0) {
            $signedUrl .= '?';
            if (count($signedHeader) > 0) {
                foreach ($request->queries as $key => $val) {
                    $signedUrl .= $key . '=' . $val . '&';
                }
            } else {
                foreach ($request->queries as $key => $val) {
                    $signedUrl .= rawurlencode($key) . '=' . rawurlencode($val) . '&';
                }
            }
            $signedUrl = substr($signedUrl, 0, strlen($signedUrl) - 1);
        }
        $output = new PreSignedURLOutput($signedUrl, $signedHeader);
        return $output;
    }
}

