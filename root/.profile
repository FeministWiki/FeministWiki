# ~/.profile: executed by Bourne-compatible login shells.

export PATH=$HOME/bin:$PATH
export EDITOR=mg
export LC_COLLATE=POSIX

if [ "$BASH" ] && [ -f ~/.bashrc ]
then
    . ~/.bashrc
fi

mesg n 2> /dev/null || true
