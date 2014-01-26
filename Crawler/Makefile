all:
	javac -encoding "UTF-8" -d ./TerminalBin/ -classpath "./*" src/*.java

archiveMasterList:
	java -classpath "./TerminalBin/:./*" Main archiveMasterList IACM
	java -classpath "./TerminalBin/:./*" Main archiveMasterList Qoos

updateMasterList:
	java -classpath "./TerminalBin/:./*" Main updateMasterList IACM
	java -classpath "./TerminalBin/:./*" Main updateMasterList Qoos

removeDoc:
	rm ArticleTmp/IACM/*
	rm ArticleTmp/Qoos/*

crawlDoc:
	java -classpath "./TerminalBin/:./*" Main crawlDoc IACM
	java -classpath "./TerminalBin/:./*" Main crawlDoc Qoos