<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings em português do Brasil para Video Notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addnoteatcurrenttime'] = 'Adicionar nota neste momento';
$string['allowseek'] = 'Permitir avanço livre';
$string['category'] = 'Categoria';
$string['category:example'] = 'Exemplo';
$string['category:important'] = 'Importante';
$string['category:question'] = 'Dúvida';
$string['category:review'] = 'Revisar';
$string['completiondetail:notes'] = 'Criar pelo menos {$a} anotação(ões)';
$string['completiondetail:percent'] = 'Assistir pelo menos {$a}% do vídeo';
$string['completionnotes'] = 'Quantidade mínima de anotações';
$string['completionnotes_help'] = 'Defina quantas anotações o aluno deve criar para concluir a atividade. Use 0 para desativar esta regra.';
$string['completionnoteserror'] = 'A quantidade mínima deve ser maior que zero, ou ser 0 para desativar.';
$string['completionpercent'] = 'Percentual mínimo assistido';
$string['completionpercent_help'] = 'Defina de 1 a 100 para exigir um percentual mínimo de conteúdo único realmente assistido. Use 0 para desativar esta regra.';
$string['completionpercenterror'] = 'O percentual assistido deve ficar entre 1 e 100, ou ser 0 para desativar.';
$string['confirmdelete'] = 'Excluir esta anotação?';
$string['emptynote'] = 'A anotação não pode ficar vazia.';
$string['eventcoursemoduleviewed'] = 'Atividade Video Notes visualizada';
$string['exportcsv'] = 'Exportar CSV';
$string['exportednotes'] = 'Anotações organizadas pela ordem do vídeo';
$string['exporttxt'] = 'Exportar texto';
$string['invalidvimeourl'] = 'Informe uma URL válida do Vimeo.';
$string['invalidyoutubeurl'] = 'Informe uma URL válida do YouTube.';
$string['modulename'] = 'Video Notes';
$string['modulename_help'] = 'Atividade em vídeo na qual cada aluno cria anotações próprias vinculadas a momentos específicos.';
$string['modulenameplural'] = 'Video Notes';
$string['mynotes'] = 'Minhas anotações';
$string['nonotesmatch'] = 'Nenhuma anotação corresponde à pesquisa.';
$string['nonotesyet'] = 'Você ainda não criou anotações.';
$string['nosharednotes'] = 'Nenhum aluno compartilhou anotações com o professor.';
$string['note'] = 'Anotação';
$string['notemode'] = 'Privacidade das anotações';
$string['notemode_help'] = 'No modo privado, somente o aluno pode visualizar suas anotações. No compartilhamento opcional, cada anotação continua privada até o aluno marcar explicitamente que deseja compartilhá-la com os professores.';
$string['notemodeprivate'] = 'Todas as anotações são privadas';
$string['notemodeshareoptional'] = 'O aluno pode compartilhar anotações individuais com o professor';
$string['notesettings'] = 'Anotações';
$string['notetimeline'] = 'Linha do tempo das minhas anotações';
$string['playererror'] = 'Não foi possível carregar o player configurado.';
$string['pluginname'] = 'Video Notes';
$string['printnotes'] = 'Imprimir';
$string['privacy:metadata:notes'] = 'Anotações vinculadas a momentos do vídeo criadas pelo aluno.';
$string['privacy:metadata:notes:category'] = 'Categoria escolhida para a anotação.';
$string['privacy:metadata:notes:note'] = 'Texto da anotação.';
$string['privacy:metadata:notes:shared'] = 'Indica se o aluno escolheu compartilhar a anotação com professores.';
$string['privacy:metadata:notes:timecode'] = 'Posição do vídeo associada à anotação.';
$string['privacy:metadata:notes:timecreated'] = 'Data de criação da anotação.';
$string['privacy:metadata:notes:timemodified'] = 'Data da última alteração da anotação.';
$string['privacy:metadata:notes:userid'] = 'Usuário proprietário da anotação.';
$string['privacy:metadata:progress'] = 'Progresso de visualização do vídeo pelo aluno.';
$string['privacy:metadata:progress:duration'] = 'Duração conhecida do vídeo.';
$string['privacy:metadata:progress:lastposition'] = 'Última posição de reprodução.';
$string['privacy:metadata:progress:percent'] = 'Percentual de conteúdo único assistido.';
$string['privacy:metadata:progress:timecreated'] = 'Data de início do acompanhamento.';
$string['privacy:metadata:progress:timemodified'] = 'Data da última atualização do progresso.';
$string['privacy:metadata:progress:totalwatchtime'] = 'Tempo total de reprodução incluindo repetições.';
$string['privacy:metadata:progress:uniquewatched'] = 'Segundos únicos do vídeo assistidos.';
$string['privacy:metadata:progress:userid'] = 'Usuário ao qual o progresso pertence.';
$string['privacy:metadata:progress:watchedsegments'] = 'Intervalos consolidados do vídeo realmente reproduzidos.';
$string['privacy:notespath'] = 'Anotações do vídeo';
$string['privacy:progresspath'] = 'Progresso do vídeo';
$string['resumeask'] = 'Perguntar antes de continuar';
$string['resumeautomatic'] = 'Continuar automaticamente da última posição';
$string['resumefromstart'] = 'Sempre iniciar do começo';
$string['resumeno'] = 'Começar do início';
$string['resumeplayback'] = 'Retomada da reprodução';
$string['resumequestion'] = 'Você parou em {$a}. Deseja continuar deste ponto?';
$string['resumeyes'] = 'Continuar';
$string['savenoteerror'] = 'Não foi possível salvar a anotação.';
$string['searchnotes'] = 'Pesquisar nas minhas anotações';
$string['seekblocked'] = 'Você não pode avançar para uma parte que ainda não foi assistida.';
$string['shared'] = 'Compartilhada';
$string['sharednotesreport'] = 'Anotações compartilhadas';
$string['sharedwithteacher'] = 'Compartilhada com o professor';
$string['sharewithteacher'] = 'Compartilhar esta anotação com o professor';
$string['sharingdisabled'] = 'O compartilhamento de anotações está desativado nesta atividade.';
$string['sourceupload'] = 'Enviar para o Moodle';
$string['sourceurl'] = 'URL direta';
$string['sourcevimeo'] = 'Vimeo';
$string['sourceyoutube'] = 'YouTube';
$string['student'] = 'Aluno';
$string['timestamp'] = 'Momento';
$string['trackingerror'] = 'Não foi possível sincronizar o progresso do vídeo.';
$string['video'] = 'Vídeo';
$string['videofile'] = 'Arquivo de vídeo';
$string['videonotes:addinstance'] = 'Adicionar uma nova atividade Video Notes';
$string['videonotes:view'] = 'Visualizar a atividade Video Notes';
$string['videonotes:viewsharednotes'] = 'Visualizar anotações explicitamente compartilhadas pelos alunos';
$string['videonotesname'] = 'Nome da atividade';
$string['videosettings'] = 'Vídeo';
$string['videosource'] = 'Fonte do vídeo';
$string['videourl'] = 'URL do vídeo';
$string['yourprogress'] = 'Seu progresso no vídeo';
