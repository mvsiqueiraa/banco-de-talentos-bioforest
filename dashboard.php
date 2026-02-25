<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

try {
    $query = $pdo->query("SELECT * FROM candidatos ORDER BY data_cadastro DESC");
    $candidatos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestão BioForest</title>
    <link rel="icon" type="image/svg+xml" href="embrapii.svg">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="https://bioforest.com.br"><img src="embrapii.svg" alt="BioForest Logo"></a>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="active">Banco de Talentos</a>
            <a href="logout.php" class="logout">Sair do Sistema</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <h2>Gestão de Talentos</h2>
            <div class="user-profile">
                <span>Coordenação BioForest</span>
                <div class="avatar">C</div>
            </div>
        </header>

        <section class="filtros-dashboard">
            <h3>Filtros de Busca Especializados</h3>
            <div class="row-filtros" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                
                <div class="filtro-item" style="flex: 1 1 200px;">
                    <label>Buscar por Nome</label>
                    <input type="text" id="filtro-nome" placeholder="Digite o nome..." onkeyup="aplicarFiltros()" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--cinza-borda);">
                </div>

                <div class="filtro-item" style="flex: 1 1 200px;">
                    <label>Área de Atuação</label>
                    <select id="filtro-area" onchange="aplicarFiltros()">
                        <option value="todos">Todas as Áreas</option>
                        <option value="Computação">Computação / TI</option>
                        <option value="Engenharias">Engenharias</option>
                        <option value="Biotecnologia">Biotecnologia</option>
                        <option value="Ciências Ambientais">Ciências Ambientais / Florestal</option>
                        <option value="Administração">Administração / Gestão</option>
                        <option value="Outra">Outra</option>
                    </select>
                </div>

                <div class="filtro-item" style="flex: 1 1 200px;">
                    <label>Nível de Formação</label>
                    <select id="filtro-nivel" onchange="aplicarFiltros()">
                        <option value="todos">Todos os Níveis</option>
                        <option value="Técnico">Técnico</option>
                        <option value="Graduação">Graduação</option>
                        <option value="Mestrado">Mestrado</option>
                        <option value="Doutorado">Doutorado</option>
                        <option value="Pós-Doutorado">Pós-doutorado</option>
                        <option value="Docente UFOPA">Docente UFOPA</option>
                        <option value="Docente Externo">Docente externo</option>
                        <option value="Servidor UFOPA">Servidor UFOPA</option>
                    </select>
                </div>

                <div class="filtro-item" style="flex: 1 1 150px;">
                    <label>Vínculo UFOPA</label>
                    <select id="filtro-vinculo" onchange="aplicarFiltros()">
                        <option value="todos">Todos</option>
                        <option value="Sim">Sim</option>
                        <option value="Nao">Não</option>
                    </select>
                </div>

                <div class="filtro-item" style="flex: 1 1 250px;">
                    <label>Linha de Pesquisa</label>
                    <select id="filtro-linha" onchange="aplicarFiltros()">
                        <option value="todos">Todas</option>
                        <option value="Produtos madeireiros e seus derivados">Produtos madeireiros e derivados</option>
                        <option value="Bioalimentos">Bioalimentos</option>
                        <option value="Biocosméticos">Biocosméticos</option>
                        <option value="Plataformas computacionais para o setor florestal">Plataformas computacionais</option>
                        <option value="Bioprocessos e biotecnologias microbianas">Bioprocessos / Biotecnologias</option>
                        <option value="Outra">Outra</option>
                    </select>
                </div>

                <div class="filtro-item" style="flex: 1 1 150px;">
                    <label>Disponibilidade</label>
                    <select id="filtro-disponibilidade" onchange="aplicarFiltros()">
                        <option value="todos">Todas</option>
                        <option value="Sim">Sim</option>
                        <option value="Não">Não</option>
                        <option value="Apenas Viagens">Apenas Viagens</option>
                    </select>
                </div>

                <div class="filtro-item" style="flex: 1 1 150px;">
                    <label>É PcD?</label>
                    <select id="filtro-pcd" onchange="aplicarFiltros()">
                        <option value="todos">Todos</option>
                        <option value="sim">Sim</option>
                        <option value="nao">Não</option>
                    </select>
                </div>

                <div class="filtro-item btn-box" style="flex: 1 1 150px;">
                    <button onclick="exportarExcel()" style="background-color: #107c41; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        📥 Baixar Excel
                    </button>
                </div>
            </div>
        </section>

        <section class="tabela-container" style="margin-top: 20px;">
            <table class="tabela-talentos" id="tabelaCandidatos">
                <thead>
                    <tr>
                        <th>Candidato</th>
                        <th>Nível</th>
                        <th>Área</th>
                        <th>Vínculo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="corpoTabela">
                    <?php foreach($candidatos as $c): ?>
                    <tr data-area="<?php echo htmlspecialchars($c['area']); ?>" 
                        data-nivel="<?php echo htmlspecialchars($c['nivel']); ?>"
                        data-vinculo="<?php echo htmlspecialchars($c['vinculo_ufopa']); ?>"
                        data-linha="<?php echo htmlspecialchars($c['linha_pesquisa']); ?>"
                        data-disponibilidade="<?php echo htmlspecialchars($c['disponibilidade']); ?>"
                        data-pcd="<?php echo ($c['pcd'] != 'Nao' ? 'sim' : 'nao'); ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($c['nome']); ?></strong><br>
                            <span class="email-text"><?php echo htmlspecialchars($c['email']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($c['nivel']); ?></td>
                        <td><?php echo htmlspecialchars($c['area']); ?></td>
                        <td><?php echo ($c['vinculo_ufopa'] == 'Sim' ? 'UFOPA' : 'Externo'); ?></td>
                        <td>
                            <?php 
                            $statusClass = 'badge-geral';
                            $legenda = '';
        
                            if($c['status'] == 'Novo') { 
                                $statusClass = 'badge-novo'; 
                                $legenda = 'Candidato recém-cadastrado, aguardando primeira avaliação.';
                            }
                            if($c['status'] == 'Em Análise') { 
                                $statusClass = 'badge-analise'; 
                                $legenda = 'O perfil está sendo analisado pela coordenação.';
                            }
                            if($c['status'] == 'Aprovado') { 
                                $statusClass = 'badge-aprovado'; 
                                $legenda = 'Candidato aprovado para compor o Banco de Talentos.';
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>" title="<?php echo $legenda; ?>">
                                <?php echo $c['status']; ?>
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <?php
                            $areaExibicao = ($c['area'] == 'Outra' && !empty($c['area_outra'])) ? $c['area_outra'] : $c['area'];
                            $linhaExibicao = ($c['linha_pesquisa'] == 'Outra' && !empty($c['linha_pesquisa_outra'])) ? $c['linha_pesquisa_outra'] : $c['linha_pesquisa'];
                            $dataFormata = date('d/m/Y \à\s H:i', strtotime($c['data_cadastro']));
                            ?>
                            <button class="btn-acao" onclick="abrirModal(this)"
                                data-id="<?php echo $c['id']; ?>"
                                data-nome="<?php echo htmlspecialchars($c['nome']); ?>"
                                data-email="<?php echo htmlspecialchars($c['email']); ?>"
                                data-telefone="<?php echo htmlspecialchars($c['telefone']); ?>"
                                data-cidade="<?php echo htmlspecialchars($c['cidade']); ?>"
                                data-area-exibicao="<?php echo htmlspecialchars($areaExibicao); ?>"
                                data-linha-exibicao="<?php echo htmlspecialchars($linhaExibicao); ?>"
                                data-formatura="<?php echo htmlspecialchars($c['formatura']); ?>"
                                data-idiomas="<?php echo htmlspecialchars($c['idiomas']); ?>"
                                data-tags="<?php echo htmlspecialchars($c['tags']); ?>"
                                data-status="<?php echo $c['status']; ?>" 
                                data-resumo="<?php echo htmlspecialchars($c['resumo']); ?>"
                                data-lattes="<?php echo htmlspecialchars($c['lattes']); ?>"
                                data-linkedin="<?php echo htmlspecialchars($c['linkedin']); ?>"
                                data-cadastro="<?php echo $dataFormata; ?>">
                                Detalhes
                            </button>

                            <form action="excluir_talento.php" method="POST" style="display:inline;" onsubmit="return confirm('ATENÇÃO: Tem certeza que deseja excluir permanentemente o candidato <?php echo htmlspecialchars($c['nome']); ?>? Esta ação não pode ser desfeita.');">
                                <input type="hidden" name="id_candidato" value="<?php echo $c['id']; ?>">
                                <button type="submit" class="btn-acao" style="background-color: #ef4444; margin-left: 5px;" title="Excluir Candidato">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<div id="modalPerfil" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalNome">Nome</h3>
            <button class="btn-fechar" onclick="fecharModal()">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
            <div class="perfil-info">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; background: var(--cinza-fundo); padding: 15px; border-radius: 8px; border: 1px solid var(--cinza-borda);">
                    <p style="margin: 0;"><strong>Data da Inscrição:</strong><br> <span id="modalDataCadastro" style="color: var(--verde-embrapii); font-weight: bold;"></span></p>
                    <p style="margin: 0;"><strong>Cidade:</strong><br> <span id="modalCidade"></span></p>
                    <p style="margin: 0;"><strong>E-mail:</strong><br> <span id="modalEmail"></span></p>
                    <p style="margin: 0;"><strong>Telefone:</strong><br> <span id="modalTelefone"></span></p>
                    <p style="margin: 0;"><strong>Área de Atuação:</strong><br> <span id="modalArea"></span></p>
                    <p style="margin: 0; grid-column: span 2;"><strong>Linha de Pesquisa:</strong><br> <span id="modalLinha"></span></p>
                    <p id="boxFormatura" style="margin: 0; grid-column: span 2; display: none;"><strong>Semestre de Formatura:</strong><br> <span id="modalFormatura"></span></p>
                    <p style="margin: 0; grid-column: span 2;"><strong>Idiomas:</strong><br> <span id="modalIdiomas"></span></p>
                    <p style="margin: 0; grid-column: span 2;"><strong>Linguagens / Ferramentas:</strong><br> <span id="modalTags"></span></p>
                </div>

                <p><strong>Resumo / Apresentação:</strong></p>
                <p id="modalResumo" style="background: var(--cinza-fundo); padding: 10px; border-radius: 6px; border: 1px solid var(--cinza-borda); min-height: 50px;"></p>
                
                <div class="perfil-links" style="margin-top: 15px;">
                    <a id="linkLattes" href="#" target="_blank" class="btn-link">Ver Lattes</a>
                    <a id="linkLinkedin" href="#" target="_blank" class="btn-link">Ver LinkedIn</a>
                </div>
                
                <hr>
                
                <div class="perfil-acoes">
                    <h4>Atualizar Status do Candidato</h4>
                    <form action="atualiza_status.php" method="POST" class="acao-status-container">
                        <input type="hidden" name="id_candidato" id="modalIdCandidato">
                        
                        <select name="novo_status" id="modalStatusSelect" class="select-status">
                            <option value="Novo">Novo</option>
                            <option value="Em Análise">Em Análise</option>
                            <option value="Aprovado">Aprovado</option>
                        </select>
                        
                        <button type="submit" class="btn-salvar-status">Salvar Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function aplicarFiltros() {
    const buscaNome = document.getElementById('filtro-nome') ? document.getElementById('filtro-nome').value.toLowerCase() : '';
    const area = document.getElementById('filtro-area').value;
    const nivel = document.getElementById('filtro-nivel').value;
    const vinculo = document.getElementById('filtro-vinculo').value;
    const linha = document.getElementById('filtro-linha').value;
    const disponibilidade = document.getElementById('filtro-disponibilidade').value;
    const pcd = document.getElementById('filtro-pcd').value;
    
    const linhasTabela = document.querySelectorAll('#corpoTabela tr');

    linhasTabela.forEach(linhaTabela => {
        let mostrar = true;
        const nomeCandidato = linhaTabela.querySelector('td strong').textContent.toLowerCase();

        if (buscaNome !== '' && !nomeCandidato.includes(buscaNome)) mostrar = false;
        if (area !== 'todos' && linhaTabela.dataset.area !== area) mostrar = false;
        if (nivel !== 'todos' && linhaTabela.dataset.nivel !== nivel) mostrar = false;
        if (vinculo !== 'todos' && linhaTabela.dataset.vinculo !== vinculo) mostrar = false;
        if (linha !== 'todos' && linhaTabela.dataset.linha !== linha) mostrar = false;
        if (disponibilidade !== 'todos' && linhaTabela.dataset.disponibilidade !== disponibilidade) mostrar = false;
        if (pcd !== 'todos' && linhaTabela.dataset.pcd !== pcd) mostrar = false;
        
        linhaTabela.style.display = mostrar ? '' : 'none';
    });
}

function exportarExcel() {
    const wb = XLSX.utils.table_to_book(document.getElementById('tabelaCandidatos'), {sheet: "Candidatos"});
    XLSX.writeFile(wb, "Banco_Talentos_BioForest.xlsx");
}

function abrirModal(botao) {
    const id = botao.getAttribute('data-id');
    const nome = botao.getAttribute('data-nome');
    const email = botao.getAttribute('data-email');
    const telefone = botao.getAttribute('data-telefone');
    const cidade = botao.getAttribute('data-cidade');
    const area = botao.getAttribute('data-area-exibicao');
    const linha = botao.getAttribute('data-linha-exibicao');
    const formatura = botao.getAttribute('data-formatura');
    const idiomas = botao.getAttribute('data-idiomas');
    const tags = botao.getAttribute('data-tags');
    const status = botao.getAttribute('data-status');
    const resumo = botao.getAttribute('data-resumo');
    const lattes = botao.getAttribute('data-lattes');
    const linkedin = botao.getAttribute('data-linkedin');
    const dataCadastro = botao.getAttribute('data-cadastro');
    
    document.getElementById('modalIdCandidato').value = id;
    document.getElementById('modalStatusSelect').value = status;
    document.getElementById('modalNome').textContent = nome;
    
    document.getElementById('modalEmail').textContent = email;
    document.getElementById('modalTelefone').textContent = telefone;
    document.getElementById('modalCidade').textContent = cidade;
    document.getElementById('modalArea').textContent = area;
    document.getElementById('modalLinha').textContent = linha;
    document.getElementById('modalIdiomas').textContent = idiomas || 'Não informado';
    document.getElementById('modalTags').textContent = tags || 'Não informado';
    document.getElementById('modalDataCadastro').textContent = dataCadastro;
    
    const boxFormatura = document.getElementById('boxFormatura');
    if (formatura && formatura.trim() !== '') {
        document.getElementById('modalFormatura').textContent = formatura;
        boxFormatura.style.display = 'block';
    } else {
        boxFormatura.style.display = 'none';
    }

    document.getElementById('modalResumo').textContent = resumo || 'Nenhum resumo preenchido.';
    document.getElementById('linkLattes').href = lattes || '#';
    document.getElementById('linkLinkedin').href = linkedin || '#';

    document.getElementById('modalPerfil').classList.add('ativo');
}

function fecharModal() {
    document.getElementById('modalPerfil').classList.remove('ativo');
}
</script>
</body>
</html>